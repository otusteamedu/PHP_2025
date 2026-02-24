<?php

namespace Hafiz\Php2025\Factory;

use Hafiz\Php2025\Product\Product;

interface ProductFactory {
    public function createProduct(): Product;
}
