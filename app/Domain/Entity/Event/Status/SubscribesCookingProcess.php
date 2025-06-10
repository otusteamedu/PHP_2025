<?php
declare(strict_types=1);

namespace App\Domain\Entity\Event\Status;

use App\Domain\Entity\Event\ProductIsCreatedEvent;
use App\Domain\Entity\Event\SubscriberInterface;

class SubscribesCookingProcess implements SubscriberInterface
{

    public function update(ProductIsCreatedEvent $event): void
    {
        echo 'Отправляем оповещения что изменился статус приготовления продукта <br>';
    }
}
