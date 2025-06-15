<?php

namespace App\Infrastructure\Service;

use App\Domain\Notifier\SubscriberInterface;

class ProductStatusChangeService implements SubscriberInterface
{
    public function update(array $context = []): void {
        if (empty($context['status']) === false) {
            // Логика для отправки оповещения...
        }
    }
}