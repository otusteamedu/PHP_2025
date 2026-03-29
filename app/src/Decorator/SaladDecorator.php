<?php

namespace App\Decorator;

class SaladDecorator extends IngredientDecorator {
    public function getDescription(): string {
        return $this->item->getDescription() . ', Salad';
    }

    public function getCost(): float {
        return $this->item->getCost() + 0.7;
    }
}