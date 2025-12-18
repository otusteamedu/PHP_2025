<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Observer\EventInterface;
use App\Domain\Observer\SubscriberInterface;

class NotifierCooking implements SubscriberInterface
{
    public function update(EventInterface $event): void
    {
        echo $event->getCookingStatus() . PHP_EOL;
    }
}
