<?php

declare(strict_types=1);

namespace App\Shared\Domain\Publisher;

use App\Shared\Application\Publisher\PublisherInterface;
use App\Shared\Application\Subscriber\SubscriberInterface;
use App\Shared\Domain\Event\EventInterface;

class Publisher implements PublisherInterface
{
    /** @var PublisherInterface[] */
    private array $subscribers = [];

    public function subscribe(SubscriberInterface $subscriber): void
    {
        if (!in_array($subscriber, $this->subscribers, true)) {
            $this->subscribers[] = $subscriber;
        }
    }

    public function unsubscribe(SubscriberInterface $subscriber): void
    {
        // TODO: Implement unsubscribe() method.
    }

    public function handle(EventInterface $event): void
    {
        /** @var SubscriberInterface $subscriber */
        foreach ($this->subscribers as $subscriber) {
            $subscriber($event);
        }
    }
}
