<?php

namespace App\Decorator;

use App\FastFoodItemInterface;

abstract class IngredientDecorator implements FastFoodItemInterface {
    public function __construct(protected FastFoodItemInterface $item) {}
}