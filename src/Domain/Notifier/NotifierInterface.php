<?php

namespace App\Domain\Notifier;

interface NotifierInterface
{
    public function subscribe(SubscriberInterface $subscriber): void;
    public function unsubscribe(SubscriberInterface $subscriber): void;
    public function notify(): void;
}