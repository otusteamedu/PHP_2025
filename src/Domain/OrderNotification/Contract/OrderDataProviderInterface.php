<?php

declare(strict_types=1);

namespace App\Domain\OrderNotification\Contract;

use App\Domain\OrderNotification\Exception\OrderSource\OrderDataInvalidException;
use App\Domain\OrderNotification\Exception\OrderSource\OrderDataProvidingException;
use App\Domain\OrderNotification\ValueObject\Order\OrderData;
use App\Domain\OrderNotification\ValueObject\Order\OrderEvent;
use App\Domain\OrderNotification\ValueObject\Order\OrderId;

interface OrderDataProviderInterface
{
    /**
     * @throws OrderDataInvalidException
     * @throws OrderDataProvidingException
     */
    public function getOrderData(OrderId $id, OrderEvent $event): OrderData;
}
