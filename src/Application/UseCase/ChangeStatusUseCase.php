<?php

namespace Blarkinov\Hw1500\Application\UseCase;

use Blarkinov\Hw1500\Application\Observer\OrderStatusObserver;
use Blarkinov\Hw1500\Application\UseCase\Request\ChangeOrderStatusDto;
use Blarkinov\Hw1500\Domain\Observer\OrderStatusEvent;
use Blarkinov\Hw1500\Infrastructure\Gateway\FileDataBase;
use Exception;

class ChangeStatusUseCase
{

    public function change(ChangeOrderStatusDto $requestDto, OrderStatusObserver $observer): void
    {
        $db = new FileDataBase;
        $order = $db->getOrder($requestDto->getId());

        if (empty($order))
            throw new Exception('not found order id');

        $order->setStatus($requestDto->getStatus());

        $db->updateOrder($order);

        $observer->notify(new OrderStatusEvent($order->getId(), $order->getStatus()));
    }
}
