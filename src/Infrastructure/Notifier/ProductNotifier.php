<?php

namespace App\Infrastructure\Notifier;

use App\Domain\Notifier\NotifierInterface;
use App\Domain\Notifier\SubscriberInterface;

class ProductNotifier implements NotifierInterface
{
    private array $subscribers = [];

    public function __construct(private array $context) {
    }

    public function subscribe(SubscriberInterface $subscriber): void {
        $this->subscribers[] = $subscriber;
    }

    public function unsubscribe(SubscriberInterface $subscriber): void {
        // TODO: Implement unsubscribe() method.
    }

    public function notify(): void {
        foreach ($this->subscribers as $subscriber) {
            $subscriber->update($this->context);
        }
    }
}