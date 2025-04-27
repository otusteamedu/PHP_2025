<?php

namespace Hafiz\Php2025\Product;

class PizzaAdapter implements Product {
    private Pizza $pizza;

    public function __construct(Pizza $pizza) {
        $this->pizza = $pizza;
        $this->pizza->bake();
    }

    public function getDescription(): string {
        return 'Pizza';
    }

    public function getCost(): float {
        return 1000;
    }
}
