<?php

declare(strict_types=1);

namespace Otus\Code\Domain\Product\Enum;

enum Ingredient: string
{
    case Bun = 'bun';
    case Sausage = 'sausage';
    case Cutlet = 'cutlet';
    case Bread = 'bread';
    case Lettuce = 'lettuce';
    case Onion = 'onion';
    case Pepper = 'pepper';
    case Cheese = 'cheese';
    case Pickles = 'pickles';
    case Sauce = 'sauce';

    public function label(): string
    {
        return match ($this) {
            self::Bun => 'Bun',
            self::Sausage => 'Sausage',
            self::Cutlet => 'Cutlet',
            self::Bread => 'Bread',
            self::Lettuce => 'Lettuce',
            self::Onion => 'Onion',
            self::Pepper => 'Pepper',
            self::Cheese => 'Cheese',
            self::Pickles => 'Pickles',
            self::Sauce => 'Sauce',
        };
    }
}
