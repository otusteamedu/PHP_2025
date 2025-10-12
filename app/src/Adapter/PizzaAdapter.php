<?php

namespace App\Adapter;

use App\FastFoodItemInterface;

class PizzaAdapter implements FastFoodItemInterface {
    public function __construct(private Pizza $pizza) {}

    public function getDescription(): string {
        return $this->pizza->bake();
    }

    public function getCost(): float {
        return $this->pizza->price();
    }
}