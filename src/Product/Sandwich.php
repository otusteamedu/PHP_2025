<?php

namespace Hafiz\Php2025\Product;

class Sandwich implements Product {
    public function getDescription(): string {
        return 'Sandwich';
    }

    public function getCost(): float {
        return 250;
    }
}
