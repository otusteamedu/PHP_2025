<?php
namespace App\Decorator;

class WithOnion extends FoodDecorator {
    public function prepare(): string {
        return $this->food->prepare() . ", дополнительный лук";
    }
}