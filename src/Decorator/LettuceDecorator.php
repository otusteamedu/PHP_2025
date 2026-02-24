<?php

namespace Hafiz\Php2025\Decorator;

class LettuceDecorator extends ProductDecorator {
    public function getDescription(): string {
        return $this->product->getDescription() . ', Lettuce';
    }

    public function getCost(): float {
        return $this->product->getCost() + 50;
    }
}
