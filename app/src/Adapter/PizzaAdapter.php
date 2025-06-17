<?php
namespace App\Adapter;

use App\Product\Food;
use App\Pizza\Pizza;

class PizzaAdapter implements Food {
    private Pizza $pizza;

    public function __construct(Pizza $pizza) {
        $this->pizza = $pizza;
    }

    public function getName(): string {
        return $this->pizza->name();
    }

    public function getType(): string {
        return 'pizza';
    }

    public function prepare(): string {
        return $this->pizza->bake();
    }
}