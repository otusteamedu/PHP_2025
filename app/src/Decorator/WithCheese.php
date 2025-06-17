<?php
namespace App\Decorator;

class WithCheese extends FoodDecorator {
    public function prepare(): string {
        return $this->food->prepare() . ", дополнительный сыр";
    }
}