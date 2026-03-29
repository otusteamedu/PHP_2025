<?php

namespace App\Decorator;

class OnionDecorator extends IngredientDecorator {
    public function getDescription(): string {
        return $this->item->getDescription() . ', Onion';
    }

    public function getCost(): float {
        return $this->item->getCost() + 0.5;
    }
}