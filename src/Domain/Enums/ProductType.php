<?php

namespace Restaurant\Domain\Enums;

enum ProductType: string
{
    case BURGER = 'Бургер';
    case SANDWICH = 'Сэндвич';
    case HOTDOG = 'Хот-дог';
}
