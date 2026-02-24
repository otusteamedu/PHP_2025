<?php

namespace Hafiz\Php2025\Decorator;

class PepperDecorator extends ProductDecorator {
    public function getDescription(): string {
        return $this->product->getDescription() . ', Paper';
    }

    public function getCost(): float {
        return $this->product->getCost() + 10;
    }
}
