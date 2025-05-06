<?php

declare(strict_types=1);

namespace App\Core\Adapter;

class PizzaAdapter implements FastFoodItemInterface
{
    private Pizza $pizza;

    public function __construct(Pizza $pizza)
    {
        $this->pizza = $pizza;
    }

    /**
     * @return string
     */
    public function prepare(): string
    {
        return $this->pizza->makePizza();
    }
}