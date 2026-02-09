<?php

namespace Restaurant\Domain\Enums;

enum OrderStatus: string
{
    case CREATED = 'CREATED';
    case COOKING = 'COOKING';
    case READY = 'READY';
    case DELIVERED = 'DELIVERED';
}
