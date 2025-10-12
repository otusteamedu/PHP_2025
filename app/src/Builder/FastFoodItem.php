<?php

namespace App\Builder;

use App\FastFoodItemInterface;

class FastFoodItem implements FastFoodItemInterface {
    private string $description = '';
    private float $cost = 0.0;

    public function addPart(string $part, float $price): void {
        $this->description .= $part . ', ';
        $this->cost += $price;
    }

    public function getDescription(): string {
        return rtrim($this->description, ', ');
    }

    public function getCost(): float {
        return $this->cost;
    }
}