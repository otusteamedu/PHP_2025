<?php

namespace App\Domain\Enums;

enum ProductStatus: string
{
    case CREATED = 'created';
    case PREPARING = 'preparing';
    case READY = 'ready';
    case DISCARDED = 'discarded';
}
