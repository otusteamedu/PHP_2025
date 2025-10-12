<?php

namespace App\Proxy;

use App\FastFoodItemInterface;

class CookingProxy implements FastFoodItemInterface {
    public function __construct(private FastFoodItemInterface $item) {}

    public function getDescription(): string {
        $this->preCook();
        $desc = $this->item->getDescription();
        $this->postCook($desc);
        return $desc;
    }

    public function getCost(): float {
        return $this->item->getCost();
    }

    private function preCook(): void {
        echo "Checking standards.\n";
    }

    private function postCook(string $desc): void {
        if (str_contains($desc, 'Onion')) {
            echo "Onion detected — verifying freshness.\n";
        }
    }
}