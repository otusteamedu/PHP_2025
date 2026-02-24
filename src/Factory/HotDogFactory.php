<?php

namespace Hafiz\Php2025\Factory;

use Hafiz\Php2025\Product\HotDog;
use Hafiz\Php2025\Product\Product;

class HotDogFactory implements ProductFactory {
    public function createProduct(): Product {
        return new HotDog();
    }
}
