<?php declare(strict_types=1);

namespace Fastfood\Products\Entity;

class Hotdog extends BaseProduct
{
    protected string $description = "Хот-дог (булочка с сосиской)";
    protected float $cost = 125.0;

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
        return ['onion', 'mayo', 'ketchup'];
    }

}