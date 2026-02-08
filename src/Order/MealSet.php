<?php

namespace Shop\Order;

class MealSet implements OrderComponent
{
    private string $name;
    private array $children = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function add(OrderComponent $component): self
    {
        $this->children[] = $component;
        return $this;
    }

    public function remove(OrderComponent $component): void
    {
        $this->children = array_filter(
            $this->children,
            fn($child) => $child !== $component
        );
    }

    public function getPrice(): float
    {
        $total = 0;
        foreach ($this->children as $child) {
            $total += $child->getPrice();
        }
        // скидка на набор
        return $total * 0.9;
    }

    public function getDescription(): string
    {
        $descriptions = ["Набор '{$this->name}':"];
        foreach ($this->children as $child) {
            $descriptions[] = "  - " . $child->getDescription();
        }
        $descriptions[] = "Итого за набор: " . $this->getPrice();
        return implode("\n", $descriptions);
    }

    public function getName(): string
    {
        return $this->name;
    }
}