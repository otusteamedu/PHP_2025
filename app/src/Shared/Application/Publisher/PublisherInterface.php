<?php
declare(strict_types=1);


namespace App\Shared\Application\Publisher;

use App\Shared\Application\Subscriber\SubscriberInterface;
use App\Shared\Domain\Event\EventInterface;

interface PublisherInterface
{
    public function subscribe(SubscriberInterface $subscriber): void;

    public function unsubscribe(SubscriberInterface $subscriber): void;

    public function notify(EventInterface $event): void;

}