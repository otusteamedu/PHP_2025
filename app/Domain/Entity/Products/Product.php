<?php
declare(strict_types=1);

namespace App\Domain\Entity\Products;

use App\Domain\Patterns\Decorator\IngredientDecoratorInterface;

class Product implements ProductInterface
{
    protected string $name = '';
    protected float $price = 0;

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    public function setIngredient(IngredientDecoratorInterface $ingredientsProduct): void
    {
        $this->setName($ingredientsProduct->getTitle());
        $this->setPrice($ingredientsProduct->getPrice());
    }
}