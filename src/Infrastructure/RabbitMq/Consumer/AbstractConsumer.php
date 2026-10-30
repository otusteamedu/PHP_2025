<?php

declare(strict_types=1);

namespace App\Infrastructure\RabbitMq\Consumer;

use App\Infrastructure\RabbitMq\Connection\AmqpConnectionInterface;
use App\Infrastructure\RabbitMq\Exception\AmqpConnectionException;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

abstract class AbstractConsumer
{
    private ?AMQPChannel $channel = null;
    private int $deliveryNumber = 1;

    public function __construct(
        private readonly AmqpConnectionInterface $connection,
        private readonly string $queueName,
        private readonly int $maxAttempts,
        private readonly int $prefetchCount,
    ) {
    }

    abstract protected function handle(AMQPMessage $msg): HandleResult;

    /**
     * @throws AmqpConnectionException
     */
    public function consume(): void
    {
        $this->channel = $this->connection->createChannel();
        $this->registerSignalHandlers();

        $this->setupQos();
        $this->onConsumeStart();

        $handler = $this->createMessageHandler();
        $this->startConsuming($handler);
    }

    protected function getQueueName(): string
    {
        return $this->queueName;
    }

    protected function getDeliveryNumber(): int
    {
        return $this->deliveryNumber;
    }

    protected function getMaxAttempts(): int
    {
        return $this->maxAttempts;
    }

    protected function onConsumeStart(): void
    {
        fwrite(STDOUT, sprintf(
            " [*] Waiting for messages on '%s'. CTRL+C to exit.\n",
            $this->getQueueName(),
        ));
    }

    /**
     * Вызывается в начале каждой доставки, до проверки лимита доставок
     * и до handle(). Точка, в которой наследник готовит своё состояние
     * к обработке нового сообщения: сбрасывает контекст предыдущего,
     * счётчики, признак обработанности и прочее, что требуется.
     */
    protected function onDeliveryStart(): void {}

    protected function onMessageAcked(): void
    {
        fwrite(STDOUT, $this->formatOutcome('[ACK]', 'Message processed successfully'));
    }

    protected function onMessageRejected(string $reason, int $code = 0): void
    {
        fwrite(STDOUT, $this->formatOutcome('[REJECT]', $reason, $code));
    }

    protected function onMessageDropped(string $reason, int $code = 0): void
    {
        fwrite(STDOUT, $this->formatOutcome('[DROP]', $reason, $code));
    }

    protected function onError(\Throwable $e): void
    {
        fwrite(STDERR, sprintf(" [ERROR] %s: %s\n", $this->shortClass($e), $e->getMessage()));

        $previous = $e->getPrevious();
        while ($previous !== null) {
            fwrite(STDERR, sprintf("        ↳ %s: %s\n", $this->shortClass($previous), $previous->getMessage()));
            $previous = $previous->getPrevious();
        }

        fwrite(STDERR, "\n" . $e->getTraceAsString() . "\n");
    }

    protected function shortClass(object|string $obj): string
    {
        $class = is_object($obj) ? $obj::class : $obj;
        $parts = explode('\\', $class);

        return end($parts);
    }

    /**
     * Единый формат исхода: [TAG] [context] reason [code].
     * Контекст добавляет contextLabel(), причину форматирует formatReason().
     */
    protected function formatOutcome(string $tag, string $reason, int $code = 0): string
    {
        return sprintf(" %s %s%s\n", $tag, $this->contextLabel(), $this->formatReason($reason, $code));
    }

    /**
     * Контекстная метка исхода (класс исключения, идентификатор задачи и т.п.).
     * Базовая реализация пуста: транспортный слой ничего не знает о домене.
     */
    protected function contextLabel(): string
    {
        return '';
    }

    /**
     * Причина + код в едином виде: "текст" или "текст [код]".
     * Пустая причина заменяется на "Unknown error".
     */
    protected function formatReason(string $reason, int $code = 0): string
    {
        return ($reason !== '' ? $reason : 'Unknown error')
            . ($code > 0 ? sprintf(' [%d]', $code) : '');
    }

    private function registerSignalHandlers(): void
    {
        pcntl_async_signals(true);
        pcntl_signal(SIGINT, fn() => $this->channel?->close());
        pcntl_signal(SIGTERM, fn() => $this->channel?->close());
    }

    /**
     * @throws AmqpConnectionException
     */
    private function setupQos(): void
    {
        if ($this->prefetchCount <= 0) {
            return;
        }

        $this->wrapAmqpCall(
            operation: fn() => $this->channel->basic_qos(
                prefetch_size: 0,
                prefetch_count: $this->prefetchCount,
                a_global: false,
            ),
            errorMessage: 'Failed to set QoS',
        );
    }

    /**
     * @throws AmqpConnectionException
     */
    private function startConsuming(callable $handler): void
    {
        $this->wrapAmqpCall(
            operation: function() use ($handler): void {
                $this->channel->basic_consume(queue: $this->queueName, callback: $handler);
                while ($this->channel->is_consuming()) {
                    $this->channel->wait();
                }
            },
            errorMessage: 'Consumer connection lost',
        );
    }

    private function createMessageHandler(): callable
    {
        return function(AMQPMessage $msg): void
        {
            $this->onDeliveryStart();
            $this->deliveryNumber = $this->resolveDeliveryNumber($msg);

            try {
                if ($this->getDeliveryNumber() > $this->getMaxAttempts() + 1) {
                    $this->drop($msg, sprintf(
                        'Max delivery attempts exceeded (%d deliveries)',
                        $this->getDeliveryNumber(),
                    ));

                    return;
                }

                $result = $this->handle($msg);

                match ($result->status) {
                    ConsumeResult::Ack => $this->ack($msg),
                    ConsumeResult::Reject => $this->reject($msg, $result->reason, $result->code),
                    ConsumeResult::Drop => $this->drop($msg, $result->reason, $result->code),
                };

            } catch (AmqpConnectionException $e) {
                throw $e;
            } catch (\Throwable $e) {
                $this->drop($msg, 'Unhandled error: ' . $e->getMessage(), $e->getCode());
                $this->onError($e);
            }
        };
    }

    /**
     * @throws AmqpConnectionException
     */
    private function ack(AMQPMessage $msg): void
    {
        $this->wrapAmqpCall(
            operation: fn() => $this->channel->basic_ack($msg->getDeliveryTag()),
            errorMessage: 'Failed to ack message',
        );

        $this->onMessageAcked();
    }

    /**
     * @throws AmqpConnectionException
     */
    private function reject(AMQPMessage $msg, string $reason, int $code = 0): void
    {
        $this->wrapAmqpCall(
            operation: fn() => $this->channel->basic_reject($msg->getDeliveryTag(), requeue: false),
            errorMessage: 'Failed to reject message',
        );

        $this->onMessageRejected($reason, $code);
    }

    /**
     * Технически идентичен ack() - оба вызывают basic_ack.
     * Разница только в хуке: onMessageDropped вместо onMessageAcked.
     * Семантически - отбраковка, а не успех.
     *
     * @throws AmqpConnectionException
     */
    private function drop(AMQPMessage $msg, string $reason, int $code = 0): void
    {
        $this->wrapAmqpCall(
            operation: fn() => $this->channel->basic_ack($msg->getDeliveryTag()),
            errorMessage: 'Failed to drop message',
        );

        $this->onMessageDropped($reason, $code);
    }

    /**
     * @throws AmqpConnectionException
     */
    private function wrapAmqpCall(callable $operation, string $errorMessage): void
    {
        try {
            $operation();
        } catch (AmqpConnectionException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new AmqpConnectionException($errorMessage . ': ' . $e->getMessage(), previous: $e);
        }
    }

    /**
     * Номер текущей доставки (1-based): первая доставка = 1,
     * каждый reject-цикл через retry-очередь увеличивает счётчик.
     */
    private function resolveDeliveryNumber(AMQPMessage $msg): int
    {
        return (int) ($this->lastDeathForQueue($msg)['count'] ?? 0) + 1;
    }

    /**
     * Запись x-death о "смерти" сообщения в основной очереди (queue = текущая).
     * Первое совпадение - актуальная запись для этой очереди.
     *
     * @return array<string, mixed>|null
     */
    private function lastDeathForQueue(AMQPMessage $msg): ?array
    {
        return array_find(
            $this->extractDeaths($msg),
            fn($death) => ($death['queue'] ?? '') === $this->getQueueName(),
        );
    }

    /**
     * Записи x-death о "смертях" сообщения.
     * Каждая запись может быть AMQPTable или массивом в зависимости от драйвера.
     *
     * @return list<array<string, mixed>>
     */
    private function extractDeaths(AMQPMessage $msg): array
    {
        if (!$msg->has('application_headers')) {
            return [];
        }

        $headers = $msg->get('application_headers');
        if (!$headers instanceof AMQPTable) {
            return [];
        }

        $deaths = $headers->getNativeData()['x-death'] ?? [];

        return array_map(
            static fn($death) => $death instanceof AMQPTable
                ? $death->getNativeData()
                : (is_array($death) ? $death : []),
            $deaths,
        );
    }

    /**
     * Возраст ретрая: секунды с первой "смерти" сообщения в основной очереди.
     * Время в записи x-death фиксируется при первой "смерти" и не обновляется
     * далее, поэтому метка накопительная, null - сообщение ещё не умирало.
     */
    protected function measureRetryAge(AMQPMessage $msg): ?float
    {
        $diedAt = $this->lastDeathForQueue($msg)['time'] ?? null;

        if ($diedAt instanceof \DateTimeInterface) {
            $diedAt = (float) $diedAt->format('U.u');
        }

        if (!is_numeric($diedAt)) {
            return null;
        }

        return max(0.0, microtime(true) - (float) $diedAt);
    }
}
