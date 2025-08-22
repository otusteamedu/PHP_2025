<?php

declare(strict_types=1);

namespace App\Adapters;

interface PizzaInterface
{
    public string $crust { get; }
    public string $sauce { get; }
    public array $toppings { get; }
    public int $basePrice { get; }
    public function addTopping(string $topping, int $price): void;
    public function getTotalCost(): int;
}
