<?php declare(strict_types=1);

namespace Fastfood\Products\Entity;

class Burger extends BaseProduct
{
    protected string $description = "Бургер (булочка с котлетой)";
    protected float $cost = 150.0;

    /**
     * @param string $description
     * @param float $cost
     * @return void
     */
    public function setBase(string $description, float $cost): void
    {
        $this->description = $description;
        $this->cost = $cost;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getCost(): float
    {
        return $this->cost;
    }

    public function getBasicIngredients(): array
    {
        return ['cheese', 'salad', 'onion', 'tomato', 'ketchup'];
    }

}