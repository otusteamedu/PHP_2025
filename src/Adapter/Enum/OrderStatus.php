<?php

namespace Shop\Adapter\Enum;

enum OrderStatus: string
{
    case CREATED = 'created';
    case PROCESSING = 'processing';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';
}
