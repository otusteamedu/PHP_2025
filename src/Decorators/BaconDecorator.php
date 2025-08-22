<?php

declare(strict_types=1);

namespace App\Decorators;

class BaconDecorator extends AbstractProductDecorator
{
    private int $baconPrice = 120;

    public string $name {
        get {
            return $this->product->name . ' с беконом';
        }
    }

    public int $price {
        get {
            return $this->product->price + $this->baconPrice;
        }
    }

    public string $description {
        get {
            return $this->product->description . ' + бекон';
        }
    }
}
