<?php

declare(strict_types=1);

namespace MkdBot\Infrastructure\Queue;

use MkdBot\Application\DTO\NewsDeliveryDTO;
use MkdBot\Application\UseCase\SendNewsToUser;
use MkdBot\Domain\Interface\DatabaseConnectionInterface;
use MkdBot\Domain\Interface\FallbackMessageRepositoryInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;

/**
 * Consumer рассылки новостей — обрабатывает сообщения из mkd.news.delivery
 * Читает NewsDeliveryDTO, вызывает SendNewsToUser,
 * при успехе markAsSent, при ошибке markAsFailed + NACK для retry
 */
class NewsDeliveryConsumer extends RabbitMQConsumer
{
    public function __construct(
        RabbitMQConnectionFactory $connectionFactory,
        FallbackMessageRepositoryInterface $fallbackRepo,
        LoggerInterface $logger,
        private readonly SendNewsToUser $sendNewsToUser,
        ?DatabaseConnectionInterface $dbConnection = null,
    ) {
        parent::__construct($connectionFactory, 'mkd.news.delivery', $fallbackRepo, $logger, $dbConnection);
    }

    protected function processMessage(string $body, array $headers): void
    {
        $data = json_decode($body, true);
        if ($data === null) {
            throw new RuntimeException("Не удалось декодировать JSON сообщения рассылки: " . $body);
        }

        $dto = NewsDeliveryDTO::fromArray($data);

        $this->logger->info("Обработка доставки новости: newsId={$dto->newsId}, userId={$dto->userId}");

        $this->sendNewsToUser->execute($dto);
    }
}
