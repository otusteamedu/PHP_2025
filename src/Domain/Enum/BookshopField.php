<?php

declare(strict_types=1);

namespace App\Domain\Enum;

enum BookshopField: string
{
    case Sku = 'sku';
    case Title = 'title';
    case Category = 'category';
    case Price = 'price';
    case Stock = 'stock';
    case Shop = 'shop';

    public function nestedValue(): string
    {
        return match ($this) {
            self::Stock => "$this->value.$this->value",
            self::Shop => self::Stock->value . '.' . $this->value,
            default => throw new \LogicException('Only "stock" or "shop" can be nested.'),
        };
    }
}
