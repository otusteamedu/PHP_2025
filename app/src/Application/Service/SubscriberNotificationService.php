<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\EventListener\EventListenerInterface;
use App\Domain\Event\NewsIsCreatedEvent;
use App\Domain\Repository\CategoryRepositoryInterface;
use App\Domain\Repository\SubscriptionRepositoryInterface;
use Psr\Log\LoggerInterface;

readonly class SubscriberNotificationService implements EventListenerInterface
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
        private SubscriptionRepositoryInterface $subscriptionRepository,
        private LoggerInterface $logger,
    )
    {
    }

    public function handle(object $event): void
    {
        if (!$event instanceof NewsIsCreatedEvent) {
            return;
        }

        $category = $this->categoryRepository->findOneById($event->category_id);
        if ($category === null) {
            return;
        }

        $subscriptions = $this->subscriptionRepository->findAllByCategory($category);
        foreach ($subscriptions as $subscription) {
            $this->logger->info('Email with notification about news «{newsTitle}» has sent to email «{userEmail}»', [
                'newsTitle' => $event->title,
                'userEmail' => $subscription->getEmail()->getValue(),
            ]);
        }
    }

    public function getSubscribedEvents(): array
    {
        return [
            NewsIsCreatedEvent::class,
        ];
    }
}
