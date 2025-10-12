<?php

namespace App\Products;

// Внешний класс пиццы с несовместимым интерфейсом
class Pizza
{
    private string $type;
    private array $toppings;

    public function __construct(string $type)
    {
        $this->type = $type;
        $this->toppings = ['dough', 'sauce', 'cheese'];
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getToppings(): array
    {
        return $this->toppings;
    }

    public function addTopping(string $topping): void
    {
        $this->toppings[] = $topping;
    }

    public function bake(): string
    {
        return "Baking {$this->type} pizza";
    }
}
