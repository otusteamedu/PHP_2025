<?php

declare(strict_types=1);

namespace MkdBot\Infrastructure\Queue;

use MkdBot\Domain\Entity\FallbackMessage;
use MkdBot\Domain\Enum\QueueNameType;
use MkdBot\Domain\Interface\DatabaseConnectionInterface;
use MkdBot\Domain\Interface\FallbackMessageRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Consumer очереди mkd.fallback (DLQ) — записывает неудачные сообщения в БД для ручного разбора
 */
class FallbackConsumer extends RabbitMQConsumer
{
    public function __construct(
        string $host,
        int $port,
        string $login,
        string $password,
        string $vhost,
        FallbackMessageRepositoryInterface $fallbackRepo,
        LoggerInterface $logger,
        ?DatabaseConnectionInterface $dbConnection = null,
    ) {
        parent::__construct($host, $port, $login, $password, $vhost, QueueNameType::Fallback->value, $fallbackRepo, $logger, $dbConnection);
    }

    protected function processMessage(string $body, array $headers): void
    {
        $data = json_decode($body, true);

        if ($data === null) {
            $this->logger->error("FallbackConsumer: не удалось декодировать JSON: " . $body);
            return;
        }

        $queueNameType = QueueNameType::tryFrom($data['original_queue'] ?? '') ?? QueueNameType::TelegramForward;
        $originalBody = $data['original_body'] ?? ['raw' => $body];
        $errorMessage = $data['error_message'] ?? 'Неизвестная ошибка';
        $xDeathCount = (int)($data['x_death_count'] ?? 0);

        $fallback = new FallbackMessage(
            queueName: $queueNameType,
            messageBody: $originalBody,
            errorMessage: $errorMessage,
            xDeathCount: $xDeathCount,
        );

        $this->fallbackRepo->save($fallback);
        $this->logger->info("DLQ-сообщение сохранено в БД: queue={$data['original_queue']}, error={$errorMessage}");
    }
}
