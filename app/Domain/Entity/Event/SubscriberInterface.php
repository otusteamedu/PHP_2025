<?php
declare(strict_types=1);

namespace App\Domain\Entity\Event;

interface SubscriberInterface
{
    
    public function update(ProductIsCreatedEvent $event): void;
}