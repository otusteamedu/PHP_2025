<?php

declare(strict_types=1);

namespace Restaurant\Adapter;

use Restaurant\Product\ProductInterface;

readonly class PizzaAdapter implements ProductInterface
{
    public function __construct(private PizzaInterface $pizza)
    {
    }

    public function getDescription(): string
    {
        return "Пицца ({$this->pizza->getSize()})";
    }

    public function getPrice(): float
    {
        return $this->pizza->getCost();
    }

    public function cook(): void
    {
        echo "Готовим пиццу:\n";
        $this->pizza->prepareDough();
        $this->pizza->addToppings();
        $this->pizza->bake();
    }

    public function isQualityAcceptable(): bool
    {
        return (rand(1, 10) <= 9);
    }
}
