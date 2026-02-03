<?php

namespace App\Domain\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case PREPARING = 'preparing';
    case READY = 'ready';
    case DELIVERED = 'delivered';
}