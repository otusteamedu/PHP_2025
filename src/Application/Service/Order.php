<?php

namespace App\Application\Service;

use App\Application\Strategy\ProductType;

class Order
{
    public ProductType $type;

    public bool $vegetarian;

    /** @var string[] */
    public array $optional;

    public function __construct(
        ProductType $type,
        bool $vegetarian,
        array $optional = []
    ) {
        $this->type = $type;
        $this->vegetarian = $vegetarian;
        $this->optional = $optional;
    }
}
