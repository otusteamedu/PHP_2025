<?php

declare(strict_types=1);

namespace MkdBot\Application\UseCase;

use MkdBot\Application\DTO\NewsDeliveryDTO;
use MkdBot\Domain\Interface\MaxBotClientInterface;
use MkdBot\Domain\Interface\NewsDeliveryRepositoryInterface;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * Отправка конкретной новости конкретному пользователю
 * Вызывается из consumer-а очереди mkd.news.delivery
 */
class SendNewsToUser
{
    public function __construct(
        private readonly MaxBotClientInterface $maxBot,
        private readonly NewsDeliveryRepositoryInterface $deliveryRepo,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * Отправляет новость пользователю и обновляет статус доставки
     */
    public function execute(NewsDeliveryDTO $dto): void
    {
        $this->logger->info("Отправка новости id={$dto->newsId} пользователю userId={$dto->userId}");

        try {
            $text = "📰 {$dto->title}\n\n{$dto->content}";

            $this->maxBot->sendMessageToUser($dto->userId, $text);

            $this->deliveryRepo->markAsSent($dto->newsId, $dto->userId);

            $this->logger->info("Новость id={$dto->newsId} доставлена пользователю userId={$dto->userId}");
        } catch (Throwable $e) {
            $this->deliveryRepo->markAsFailed($dto->newsId, $dto->userId);

            $this->logger->error("Ошибка доставки новости id={$dto->newsId} пользователю userId={$dto->userId}: " . $e->getMessage());

            throw $e;
        }
    }
}
