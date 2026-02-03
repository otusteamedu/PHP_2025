<?php
namespace App\Domain\Enums;

enum ProductType: string
{
    case BURGER = 'burger';
    case SANDWICH = 'sandwich';
    case HOTDOG = 'hotdog';
}