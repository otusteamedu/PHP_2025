<?php

namespace Shop\Ingredients;

use Shop\Order\OrderComponent;

class Product implements Component, OrderComponent
{
    private array $components = [];
    private string $description;

    public function __construct(string $description)
    {
        $this->description = $description;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getComponents(): array
    {
        return $this->components;
    }

    public function addComponent(Component $components): void
    {
        $this->components[] = $components;
    }

    public function getPrice(): float
    {
        $price = 0;
        foreach ($this->components as $component) {
            $price += $component->getPrice();
        }
        return $price;
    }

    public function getName(): string
    {
       return $this->description;
    }

}