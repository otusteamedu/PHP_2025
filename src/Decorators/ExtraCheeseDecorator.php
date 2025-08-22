<?php

declare(strict_types=1);

namespace App\Decorators;

class ExtraCheeseDecorator extends AbstractProductDecorator
{
    private int $extraCheesePrice = 75;

    public string $name {
        get {
            return $this->product->name . ' с дополнительным сыром';
        }
    }

    public int $price {
        get {
            return $this->product->price + $this->extraCheesePrice;
        }
    }

    public string $description {
        get {
            return $this->product->description . ' + дополнительный сыр';
        }
    }
}
