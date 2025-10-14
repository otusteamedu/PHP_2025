<?php

declare(strict_types=1);

namespace Restaurant\Adapter;

readonly class ItalianPizza implements PizzaInterface
{
    private float $cost;

    public function __construct(private string $size = 'средняя')
    {
        $this->cost = match ($size) {
            'маленькая' => 200.0,
            'средняя' => 300.0,
            'большая' => 400.0,
            default => 300.0,
        };
    }

    public function prepareDough(): void
    {
        echo "Подготавливаем итальянское тесто для пиццы\n";
    }

    public function addToppings(): void
    {
        echo "Добавляем традиционные итальянские топпинги\n";
    }

    public function bake(): void
    {
        echo "Запекаем пиццу в дровяной печи\n";
    }

    public function getSize(): string
    {
        return $this->size;
    }

    public function getCost(): float
    {
        return $this->cost;
    }
}
