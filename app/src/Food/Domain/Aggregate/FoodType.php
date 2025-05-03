<?php
declare(strict_types=1);


namespace App\Food\Domain\Aggregate;

enum FoodType: string
{
    case BURGER = 'burger';
    case HOTDOG = 'hotdog';
    case SANDWICH = 'sandwich';

}