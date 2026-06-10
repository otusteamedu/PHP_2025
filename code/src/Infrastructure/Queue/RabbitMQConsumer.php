<?php

declare(strict_types=1);

namespace MkdBot\Infrastructure\Queue;

use AMQPChannel;
use AMQPConnection;
use AMQPEnvelope;
use AMQPException;
use AMQPExchange;
use AMQPQueue;
use DateTimeImmutable;
use MkdBot\Domain\Entity\FallbackMessage;
use MkdBot\Domain\Enum\QueueNameType;
use MkdBot\Domain\Exception\ApiRateLimitExceededException;
use MkdBot\Domain\Interface\DatabaseConnectionInterface;
use MkdBot\Domain\Interface\FallbackMessageRepositoryInterface;
use PDOException;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Telegram\Bot\Exceptions\TelegramResponseException;
use Throwable;

/**
 * Базовый класс consumer-а RabbitMQ — общая логика подключения и graceful shutdown
 * Наследники реализуют processMessage()
 *
 * Подключение создаётся через RabbitMQConnectionFactory:
 * - AMQPConnection создаётся при первом вызове getConnection() фабрикой
 * - Реальное соединение — при вызове ensureConnection() в consume()
 */
abstract class RabbitMQConsumer
{
    private AMQPConnection $connection;
    private bool $shouldStop = false;
    private bool $processingMessage = false;
    private ?AMQPChannel $channel = null;
    private ?AMQPExchange $exchange = null;

    public function __construct(
        RabbitMQConnectionFactory $connectionFactory,
        private readonly string $queueName,
        protected readonly FallbackMessageRepositoryInterface $fallbackRepo,
        protected readonly LoggerInterface $logger,
        private readonly ?DatabaseConnectionInterface $dbConnection = null,
    ) {
        $this->connection = $connectionFactory->getConnection();
    }

    /**
     * Запускает consumer в цикле с graceful shutdown
     */
    public function consume(): void
    {
        // Регистрация обработчиков сигналов для graceful shutdown
        if (function_exists('pcntl_signal')) {
            pcntl_signal(SIGTERM, function (): void {
                $this->shouldStop = true;
                $this->waitForProcessing();
            });
            pcntl_signal(SIGINT, function (): void {
                $this->shouldStop = true;
                $this->waitForProcessing();
            });
            pcntl_async_signals(true);
        }

        $this->logger->info("Consumer запущен: очередь={$this->queueName}");

        try {
            $this->ensureConnection();
            $this->channel = new AMQPChannel($this->connection);
            $this->channel->setPrefetchCount(1);

            $queue = new AMQPQueue($this->channel);
            $queue->setName($this->queueName);
            $queue->setFlags(AMQP_DURABLE);

            while (!$this->shouldStop) {
                try {
                    $message = $queue->get(AMQP_NOPARAM);

                    if ($message === null) {
                        // Нет сообщений — ждём 1 сек
                        usleep(1000000);

                        // Обработка сигналов
                        if (function_exists('pcntl_signal_dispatch')) {
                            pcntl_signal_dispatch();
                        }
                        continue;
                    }

                    $this->processingMessage = true;
                    try {
                        $this->handleMessage($queue, $message);
                    } finally {
                        $this->processingMessage = false;
                    }
                } catch (AMQPException $e) {
                    $this->logger->error("Ошибка чтения из очереди: " . $e->getMessage());
                    usleep(5000000); // 5 сек пауза при ошибке
                }

                // Обработка сигналов
                if (function_exists('pcntl_signal_dispatch')) {
                    pcntl_signal_dispatch();
                }
            }

            $this->logger->info("Consumer останавливается: очередь={$this->queueName}");
        } finally {
            $this->closeConnection();
        }
    }

    /**
     * Обрабатывает одно сообщение из очереди
     */
    private function handleMessage(AMQPQueue $queue, AMQPEnvelope $message): void
    {
        $deliveryTag = $message->getDeliveryTag();
        $body = $message->getBody();
        $headers = $message->getHeaders();

        // Проверка x-death count для DLQ
        $xDeathCount = $this->getXDeathCount($headers);

        // Реконнект к БД при необходимости (для долгоживущих consumer-процессов)
        if ($this->dbConnection !== null) {
            try {
                $this->dbConnection->ensureConnection();
            } catch (PDOException $e) {
                $this->logger->warning("Не удалось восстановить подключение к БД: " . $e->getMessage());
            }
        }

        try {
            $this->processMessage($body, $headers);

            // Успешная обработка — ACK
            $queue->ack($deliveryTag);
            $this->logger->debug("Сообщение обработано: queue={$this->queueName}");
        } catch (ApiRateLimitExceededException $e) {
            // 429 после исчерпания retry — повторимая ошибка -> NACK -> DLX -> retry
            if ($xDeathCount >= 3) {
                $this->logger->warning("Сообщение превысило лимит попыток ({$xDeathCount}), отправлено в DLQ: queue={$this->queueName}");
                $this->saveToFallback($body, $e->getMessage(), $xDeathCount);
                $queue->ack($deliveryTag);
            } else {
                $this->logger->warning("Rate limit, NACK (попытка {$xDeathCount}): " . $e->getMessage());
                $queue->nack($deliveryTag, AMQP_NOPARAM);
            }
        } catch (TelegramResponseException $e) {
            // Telegram API ошибка — классифицируем по HTTP-статусу
            $statusCode = $e->getHttpStatusCode();
            if ($statusCode >= 500 || $statusCode === 429) {
                // Повторимая (5xx, 429) - NACK -> DLX -> retry
                if ($xDeathCount >= 3) {
                    $this->logger->warning("Telegram {$statusCode}, превысило лимит попыток ({$xDeathCount}), DLQ: queue={$this->queueName}");
                    $this->saveToFallback($body, $e->getMessage(), $xDeathCount);
                    $queue->ack($deliveryTag);
                } else {
                    $this->logger->warning("Telegram {$statusCode}, NACK (попытка {$xDeathCount}): " . $e->getMessage());
                    $queue->nack($deliveryTag, AMQP_NOPARAM);
                }
            } else {
                // Фатальная (400, 403, 404 и т.д.) — ACK + логирование, не повторяем
                $this->logger->error("Telegram API фатальная ошибка ({$statusCode}): " . $e->getMessage());
                $this->saveToFallback($body, $e->getMessage(), $xDeathCount);
                $queue->ack($deliveryTag);
            }
        } catch (RuntimeException $e) {
            // Другие повторимые ошибки (сетевые, AMQP и т.д.) - NACK -> DLX -> retry
            if ($xDeathCount >= 3) {
                $this->logger->warning("Сообщение превысило лимит попыток ({$xDeathCount}), отправлено в DLQ: queue={$this->queueName}");
                $this->saveToFallback($body, $e->getMessage(), $xDeathCount);
                $queue->ack($deliveryTag);
            } else {
                $this->logger->warning("Ошибка обработки, NACK (попытка {$xDeathCount}): " . $e->getMessage());
                $queue->nack($deliveryTag, AMQP_NOPARAM);
            }
        } catch (Throwable $e) {
            // Фатальная ошибка — ACK + логирование (не повторяем)
            $this->logger->error("Фатальная ошибка обработки сообщения: " . $e->getMessage());
            $this->saveToFallback($body, $e->getMessage(), $xDeathCount);
            $queue->ack($deliveryTag);
        }
    }

    /**
     * Абстрактный метод обработки сообщения — реализуется в наследниках
     */
    abstract protected function processMessage(string $body, array $headers): void;

    /**
     * Извлекает x-death count из заголовков сообщения
     */
    private function getXDeathCount(array $headers): int
    {
        if (!isset($headers['x-death'])) {
            return 0;
        }

        $xDeath = $headers['x-death'];

        if (is_array($xDeath) && !empty($xDeath)) {
            // Берём count из первой записи x-death
            $first = is_array($xDeath[0]) ? $xDeath[0] : [];
            return (int)($first['count'] ?? 1);
        }

        return 1;
    }

    /**
     * Публикует неудачное сообщение в DLQ очередь mkd.fallback через RabbitMQ
     */
    private function saveToFallback(string $body, string $errorMessage, int $xDeathCount): void
    {
        try {
            if ($this->channel === null) {
                throw new RuntimeException("Канал RabbitMQ не доступен");
            }

            $fallbackData = json_encode([
                'original_queue' => $this->queueName,
                'original_body' => json_decode($body, true) ?? ['raw' => $body],
                'error_message' => $errorMessage,
                'x_death_count' => $xDeathCount,
                'failed_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            ], JSON_UNESCAPED_UNICODE);

            if ($this->exchange === null) {
                $this->exchange = new AMQPExchange($this->channel);
                $this->exchange->setName('mkd.dlx');
            }

            $fallbackQueueName = QueueNameType::Fallback->value;
            $this->exchange->publish($fallbackData, $fallbackQueueName, AMQP_NOPARAM, ['delivery_mode' => AMQP_DURABLE]);
            $this->logger->info("Сообщение опубликовано в DLQ {$fallbackQueueName}: queue={$this->queueName}");
        } catch (Throwable $e) {
            $this->logger->error("Ошибка публикации в DLQ: " . $e->getMessage());

            // Резерв: сохраняем в БД напрямую
            try {
                $queueNameType = QueueNameType::tryFrom($this->queueName) ?? QueueNameType::TelegramForward;
                $fallback = new FallbackMessage(
                    queueName: $queueNameType,
                    messageBody: json_decode($body, true) ?? ['raw' => $body],
                    errorMessage: $errorMessage,
                    xDeathCount: $xDeathCount,
                );
                $this->fallbackRepo->save($fallback);
            } catch (Throwable $e2) {
                $this->logger->error("Ошибка fallback-сохранения в БД: " . $e2->getMessage());
            }
        }
    }

    /**
     * Ожидает завершения обработки текущего сообщения с таймаутом 10 сек
     * Вызывается из обработчика SIGTERM/SIGINT для graceful shutdown
     */
    private function waitForProcessing(): void
    {
        $timeout = 10; // совпадает с stopwaitsecs в supervisor
        $waited = 0.0;

        while ($this->processingMessage && $waited < $timeout) {
            usleep(100000); // 100 мс
            $waited += 0.1;

            // Обработка сигналов во время ожидания
            if (function_exists('pcntl_signal_dispatch')) {
                pcntl_signal_dispatch();
            }
        }

        if ($this->processingMessage) {
            $this->logger->warning("Таймаут graceful shutdown ({$timeout} сек), сообщение ещё обрабатывается: queue={$this->queueName}");
        } else {
            $this->logger->info("Текущее сообщение обработано перед завершением: queue={$this->queueName}");
        }
    }

    /**
     * Устанавливает соединение с RabbitMQ
     */
    private function ensureConnection(): void
    {
        if (!$this->connection->isConnected()) {
            $this->connection->connect();
        }
    }

    /**
     * Закрывает соединение с RabbitMQ
     */
    private function closeConnection(): void
    {
        try {
            if ($this->connection->isConnected()) {
                $this->connection->disconnect();
            }
        } catch (AMQPException $e) {
            $this->logger->error("Ошибка закрытия соединения RabbitMQ: " . $e->getMessage());
        }
    }
}
