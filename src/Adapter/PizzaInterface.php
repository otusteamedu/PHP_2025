<?php

declare(strict_types=1);

namespace Restaurant\Adapter;

interface PizzaInterface
{
    public function prepareDough(): void;

    public function addToppings(): void;

    public function bake(): void;

    public function getSize(): string;

    public function getCost(): float;
}
