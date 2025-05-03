<?php
declare(strict_types=1);


namespace App\Food\Domain\Aggregate\Order;

enum FoodOrderStatusType: string
{
    case CREATED = 'created';
    case COOKING_STARTED = 'cooking_started';
    case COOKING_COMPLETED = 'cooking_completed';
    case CANCELLED = 'cancelled';
    case PAID = 'paid';
    case COMPLETED = 'completed';


}