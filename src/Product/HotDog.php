<?php

namespace Hafiz\Php2025\Product;

class HotDog implements Product {
    public function getDescription(): string {
        return 'HotDog';
    }

    public function getCost(): float {
        return 300;
    }
}
