<?php
namespace App\Decorator;

class WithPepper extends FoodDecorator {
    public function prepare(): string {
        return $this->food->prepare() . ", дополнительный перец";
    }
}