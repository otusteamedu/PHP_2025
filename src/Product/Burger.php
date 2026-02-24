<?php

namespace Hafiz\Php2025\Product;

class Burger implements Product {
    public function getDescription(): string {
        return 'Burger';
    }

    public function getCost(): float {
        return 350;
    }
}
