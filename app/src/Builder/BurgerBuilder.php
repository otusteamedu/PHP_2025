<?php

namespace App\Builder;

use App\FastFoodItemInterface;

class BurgerBuilder implements FastFoodBuilderInterface {
    private FastFoodItem $item;

    public function reset(): void {
        $this->item = new FastFoodItem();
    }

    public function setBread(): void {
        $this->item->addPart('Burger bun', 1.0);
    }

    public function setMeat(): void {
        $this->item->addPart('Beef patty', 3.0);
    }

    public function getProduct(): FastFoodItemInterface {
        return $this->item;
    }
}