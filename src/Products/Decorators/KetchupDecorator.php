<?php declare(strict_types=1);

namespace Fastfood\Products\Decorators;

class KetchupDecorator extends ProductDecorator implements IngredientDecoratorInterface
{
    private string $key = 'ketchup';
    private string $name = 'кетчуп';
    private float $price = 19.0;

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