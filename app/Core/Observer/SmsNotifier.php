<?php

declare(strict_types=1);

namespace App\Core\Observer;

use App\Models\Order;
use App\Services\SmsService;

readonly class SmsNotifier implements OrderObserverInterface
{
    public function __construct(private SmsService $smsService) {}

    public function update(Order $order): void
    {
        if ($order->getStatus() != 'created') {
            $this->smsService->send($order->getClientPhone(),
                "Текущий статус заказа #{$order->getId()}  {$order->getStatus()}"
            );
        }
    }
}