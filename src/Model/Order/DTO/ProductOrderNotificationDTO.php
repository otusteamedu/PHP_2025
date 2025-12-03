<?php
declare(strict_types=1);

namespace Dinargab\Homework15\Model\Order\DTO;

use Dinargab\Homework15\Model\Order\OrderStatus;

class ProductOrderNotificationDTO
{
    public function __construct(
        public int         $orderId,
        public OrderStatus $orderStatus,
    )
    {

    }
}