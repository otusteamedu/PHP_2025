<?php

namespace Hafiz\Php2025\Decorator;

class OnionDecorator extends ProductDecorator {
    public function getDescription(): string {
        return $this->product->getDescription() . ', Onion';
    }

    public function getCost(): float {
        return $this->product->getCost() + 30;
    }
}
