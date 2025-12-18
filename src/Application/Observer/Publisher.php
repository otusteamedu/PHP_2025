<?php

declare(strict_types=1);

namespace App\Application\Observer;

use App\Domain\Observer\EventInterface;
use App\Domain\Observer\PublisherInterface;
use App\Domain\Observer\SubscriberInterface;

final class Publisher implements PublisherInterface
{
    private array $subscribers = [];

    public function subscribe(SubscriberInterface $subscriber): void
    {
        $this->subscribers[] = $subscriber;
    }

    public function unsubscribe(SubscriberInterface $subscriber): void
    {
    }

    public function notify(EventInterface $event): void
    {
        /** @var SubscriberInterface $subscriber */
        foreach ($this->subscribers as $subscriber) {
            $subscriber->update($event);
        }
    }
}
