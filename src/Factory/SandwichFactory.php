<?php

namespace Hafiz\Php2025\Factory;

use Hafiz\Php2025\Product\Sandwich;
use Hafiz\Php2025\Product\Product;

class SandwichFactory implements ProductFactory {
    public function createProduct(): Product {
        return new Sandwich();
    }
}
