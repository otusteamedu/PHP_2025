<?php
namespace App\Decorator;

class WithLettuce extends FoodDecorator {
    public function prepare(): string {
        return $this->food->prepare() . ", дополнительный салат";
    }
}