<?php
declare(strict_types=1);

namespace App\Domain\Patterns\Decorator;

class LettuceIngredientDecorator implements IngredientDecoratorInterface
{

    private const INGREDIENT = 'салат';
    private const PRICE = 30;

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