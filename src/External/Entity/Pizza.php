<?php

namespace Shop\External\Entity;
class Pizza
{
    private array $toppings = [];
    private array $backingTemperature = [];

    private string $dough;

    public function __construct(
        private string $name,
    )
    {
    }

    public function addTopping(string $name): void
    {
        $this->toppings[] = $name;
    }

    public function getToppings(): array
    {
        return $this->toppings;
    }

    public function getBackingTemperature(): array
    {
        return $this->backingTemperature;
    }

    public function setBackingTemperature(array $backingTemperature): void
    {
        $this->backingTemperature = $backingTemperature;
    }

    public function getDough(): string
    {
        return $this->dough;
    }

    public function setDough(string $dough): void
    {
        $this->dough = $dough;
    }

    public function getName(): string
    {
        return $this->name;
    }

}