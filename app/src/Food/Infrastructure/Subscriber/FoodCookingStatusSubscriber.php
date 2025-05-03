<?php
declare(strict_types=1);


namespace App\Food\Infrastructure\Subscriber;

use App\Food\Domain\Event\FoodCookingStatusUpdated;
use App\Shared\Application\Subscriber\SubscriberInterface;
use App\Shared\Domain\Event\EventInterface;

class FoodCookingStatusSubscriber implements SubscriberInterface
{

    public function __invoke(EventInterface $event): void
    {
        if ($this->supportsEvent($event)) {
            //todo например, сделать так чтобы он менял статус заказа, если вся еда готова
           sleep(1);
        }

    }

    public function supportsEvent(EventInterface $event): bool
    {
        return $event instanceof FoodCookingStatusUpdated;
    }
}