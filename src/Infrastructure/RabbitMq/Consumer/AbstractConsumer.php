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
    private int $attemptNumber = 1;

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

    protected function getAttemptNumber(): int
    {
        return $this->attemptNumber;
    }

    protected function getMaxAttempts(): int
    {
        return $this->maxAttempts;
    }

    protected function onConsumeStart(): void
    {
        fwrite(STDOUT, sprintf(
            " [*] Waiting for messages on '%s'. CTRL+C to exit.\n",
            $this->queueName,
        ));
    }

    protected function onMessageAcked(): void
    {
        fwrite(STDOUT, " [ACK] Message processed successfully\n");
    }

    protected function onMessageRejected(string $reason, int $code = 0): void
    {
        fwrite(STDERR, sprintf(
            " [REJECT] (attempt %d/%d) %s%s\n",
            $this->attemptNumber,
            $this->maxAttempts,
            $reason !== '' ? $reason : 'Unknown error',
            $code > 0 ? " [$code]" : '',
        ));
    }

    protected function onMessageDropped(string $reason, int $code = 0): void
    {
        fwrite(STDERR, sprintf(
            " [DROP] %s%s\n",
            $reason !== '' ? $reason : 'Unknown error',
            $code > 0 ? " [$code]" : '',
        ));
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

    private function shortClass(object $obj): string
    {
        $parts = explode('\\', $obj::class);

        return end($parts);
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
            $this->attemptNumber = $this->resolveAttemptNumber($msg);

            try {
                if ($this->attemptNumber > $this->maxAttempts) {
                    $this->drop($msg, sprintf('Max attempts (%d) exceeded', $this->maxAttempts));
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
     * Возвращает номер текущей попытки (1-based).
     * Первая доставка - attempt 1, каждый retry увеличивает счётчик.
     */
    private function resolveAttemptNumber(AMQPMessage $msg): int
    {
        if (!$msg->has('application_headers')) {
            return 1;
        }

        $headers = $msg->get('application_headers');

        if (!$headers instanceof AMQPTable) {
            return 1;
        }

        $nativeData = $headers->getNativeData();
        $xdeaths = $nativeData['x-death'] ?? [];

        foreach ($xdeaths as $death) {
            $deathData = $death instanceof AMQPTable
                ? $death->getNativeData()
                : (is_array($death) ? $death : []);

            if (($deathData['queue'] ?? '') === $this->queueName) {
                return (int) ($deathData['count'] ?? 0) + 1;
            }
        }

        return 1;
    }
}
