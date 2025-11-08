<?php
namespace App\Decorator;

use App\Product\Food;

abstract class FoodDecorator implements Food {
    protected Food $food;

    public function __construct(Food $food) {
        $this->food = $food;
    }

    public function getName(): string {
        return $this->food->getName();
    }

    public function prepare(): string {
        return $this->food->prepare(); 
    }

    public function getType(): string {
        return $this->food->getType();
    }
}
