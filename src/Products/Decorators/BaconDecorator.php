<?php declare(strict_types=1);

namespace Fastfood\Products\Decorators;

class BaconDecorator extends ProductDecorator implements IngredientDecoratorInterface
{
    private string $key = 'bacon';
    private string $name = 'бекон';
    private float $price = 30.0;

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->product->getDescription() . ', ' . $this->name;
    }

    /**
     * @return float
     */
    public function getCost(): float
    {
        return $this->product->getCost() + $this->price;
    }
}