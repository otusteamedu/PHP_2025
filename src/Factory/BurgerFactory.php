<?php

namespace Hafiz\Php2025\Factory;

use Hafiz\Php2025\Product\Burger;
use Hafiz\Php2025\Product\Product;

class BurgerFactory implements ProductFactory {
    public function createProduct(): Product {
        return new Burger();
    }
}
