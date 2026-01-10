<?php declare(strict_types=1);

namespace App\Core;

enum FoodType: string
{
    case BURGER = 'burger';
    case SANDWICH = 'sandwich';
    case HOT_DOG = 'hot_dog';
}
