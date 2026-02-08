<?php

namespace Shop\Observer\Publisher;

use Shop\Observer\Subscribers\Subscriber;

class Publisher
{

    private array $subscribes = [];

    public function subscribe(Subscriber $subscriber, string $eventType = '*'): void
    {
        $this->subscribes[$eventType][] = $subscriber;
    }

    public function unsubscribe(Subscriber $subscriber, string $eventType = '*'): void
    {
        if (!isset($this->subscribes[$eventType])) {
            return;
        }

        $key = $this->getKeySubscriber($subscriber);

        if ($this->subscribes[$eventType][$key]) {
            unset($this->subscribes[$eventType][$key]);
        }

    }

    private function getKeySubscriber(Subscriber $subscriber): int|null
    {
        foreach ($this->subscribes as $key => $events) {
            foreach ($events as $item) {
                if ($subscriber::class == $item::class) {
                    return (int)$key;
                }
            }

        }
        return null;
    }

    public function notify(string $eventType): void
    {
        if (isset($this->subscribes[$eventType])) {
            foreach ($this->subscribes[$eventType] as $subscriber) {
                $subscriber->execute();
            }
        }

        if (isset($this->subscribes['*'])) {
            foreach ($this->subscribes['*'] as $subscriber) {
                $subscriber->execute();
            }
        }
    }
}