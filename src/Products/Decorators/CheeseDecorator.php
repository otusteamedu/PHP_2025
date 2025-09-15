<?php declare(strict_types=1);

namespace Fastfood\Products\Decorators;

class CheeseDecorator extends ProductDecorator implements IngredientDecoratorInterface
{
    private string $key = 'cheese';
    private string $name = 'сыр';
    private float $price = 25.0;

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