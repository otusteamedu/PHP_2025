<?php
declare(strict_types=1);

namespace App\Domain\Patterns\Decorator;

class OnionIngredientDecorator implements IngredientDecoratorInterface
{

    private const INGREDIENT = 'лук';
    private const PRICE = 15;

    public function __construct(
        private readonly IngredientDecoratorInterface $ingredient,
    )
    {
    }

    public function getTitle(): string
    {
        return $this->ingredient->getTitle() . ', ' . self::INGREDIENT;
    }

    public function getPrice(): float
    {
        return $this->ingredient->getPrice() + self::PRICE;
    }
}