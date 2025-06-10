<?php
declare(strict_types=1);

namespace App\Domain\Patterns\Observer;

use App\Domain\Entity\Event\SubscriberInterface;
use App\Domain\Entity\Event\ProductIsCreatedEvent;

interface PublisherInterface
{
    public function subscribe(SubscriberInterface $subscriber): void;

    public function unsubscribe(SubscriberInterface $subscriber): void;

    public function notify(ProductIsCreatedEvent $event): void;
}