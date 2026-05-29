<?php

declare(strict_types=1);

namespace MkdBot\Application\UseCase;

use MkdBot\Application\DTO\NewsDeliveryDTO;
use MkdBot\Domain\Entity\News;
use MkdBot\Domain\Entity\NewsDelivery;
use MkdBot\Domain\Enum\NewsDeliveryStatus;
use MkdBot\Domain\Enum\QueueNameType;
use MkdBot\Domain\Interface\BotSubscriberRepositoryInterface;
use MkdBot\Domain\Interface\NewsDeliveryRepositoryInterface;
use MkdBot\Domain\Interface\NewsRepositoryInterface;
use MkdBot\Domain\Interface\QueuePublisherInterface;
use Psr\Log\LoggerInterface;

/**
 * Оркестрация рассылки новостей — берёт pending-новости,
 * для каждой находит активных подписчиков без доставки,
 * публикует в RabbitMQ
 */
class DeliverNews
{
    public function __construct(
        private readonly NewsRepositoryInterface $newsRepo,
        private readonly NewsDeliveryRepositoryInterface $deliveryRepo,
        private readonly BotSubscriberRepositoryInterface $subscriberRepo,
        private readonly QueuePublisherInterface $queuePublisher,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * Запускает рассылку всех pending-новостей активным подписчикам
     */
    public function execute(): void
    {
        $pendingNews = $this->newsRepo->findPending();

        if (empty($pendingNews)) {
            $this->logger->debug("Нет pending-новостей для рассылки");
            return;
        }

        $subscribers = $this->subscriberRepo->findActive();

        if (empty($subscribers)) {
            $this->logger->info("Нет активных подписчиков для рассылки");
            return;
        }

        foreach ($pendingNews as $news) {
            $this->deliverNewsToSubscribers($news, $subscribers);
        }
    }

    /**
     * Рассылает конкретную новость подписчикам
     */
    private function deliverNewsToSubscribers(News $news, array $subscribers): void
    {
        $newsId = $news->getId();
        $this->logger->info("Рассылка новости id={$newsId}: «{$news->getTitle()}»");

        // Загружаем все существующие доставки одним запросом (без N+1)
        $existingUserIds = $this->deliveryRepo->findExistingDeliveryUserIds($newsId);
        $existingSet = array_flip($existingUserIds);

        $publishedCount = 0;

        foreach ($subscribers as $subscriber) {
            $userId = $subscriber->getUserId();

            // Пропускаем, если доставка уже создана
            if (isset($existingSet[$userId])) {
                $this->logger->debug("Доставка новости id={$newsId} пользователю userId={$userId} уже существует");
                continue;
            }

            $delivery = new NewsDelivery(
                newsId: $newsId,
                userId: $userId,
                status: NewsDeliveryStatus::Pending,
            );
            $this->deliveryRepo->save($delivery);

            $dto = new NewsDeliveryDTO(
                newsId: $newsId,
                userId: $userId,
                title: $news->getTitle(),
                content: $news->getContent(),
            );

            $this->queuePublisher->publish(QueueNameType::NewsDelivery, $dto->toArray());
            $publishedCount++;
        }

        $this->newsRepo->markAsDelivering($newsId);

        $this->logger->info("Новость id={$newsId} опубликована для {$publishedCount} подписчиков");
    }
}
