<?php
declare(strict_types=1);

namespace Dinargab\Homework15\Model\Order;

enum OrderStatus
{
    case NEW;
    case COOKING;
    case COMPLETED;
    case READY;
}
