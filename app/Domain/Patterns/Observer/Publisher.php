<?php
declare(strict_types=1);

namespace App\Domain\Patterns\Observer;

use App\Domain\Entity\Event\ProductIsCreatedEvent;
use App\Domain\Entity\Event\SubscriberInterface;

class Publisher implements PublisherInterface
{

    private array $subscribers = [];

    public function subscribe(SubscriberInterface $subscriber): void
    {
        $this->subscribers[] = $subscriber;
    }

    public function unsubscribe(SubscriberInterface $subscriber): void
    {
        // TODO: Implement unsubscribe() method.
    }

    public function notify(ProductIsCreatedEvent $event): void
    {
        foreach ($this->subscribers as $subscriber) {
            $subscriber->update($event);
        }
    }
}