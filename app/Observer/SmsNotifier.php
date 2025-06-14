<?php

declare(strict_types=1);

namespace App\Observer;

use Domain\Models\Order;
use Exception;
use Infrastructure\Services\SmsService;

readonly class SmsNotifier implements OrderObserverInterface
{
    public function __construct(private SmsService $smsService) {}

    /**
     * @throws Exception
     */
    public function update(Order $order): void
    {
        if ($order->getStatus() === 'created') {
            return;
        }
        $result = $this->smsService->send(
            $order->getClientPhone(),
            "Текущий статус заказа #{$order->getId()}  {$order->getStatus()}"
        );
        if ($result === false) {
            throw new Exception('Сообщение не отправлено');
        }
    }
}
