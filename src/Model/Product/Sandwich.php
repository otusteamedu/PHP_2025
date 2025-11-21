<?php
declare(strict_types=1);

namespace Dinargab\Homework15\Model\Product;

class Sandwich implements ProductInterface
{
    public string $bread;
    public array $fillings = [];

    public function getName(): string
    {
        return 'Sandwich';
    }

    public function getPrice(): int
    {
        return 15;
    }

    public function getDescription(): string
    {
        return "Sandwich - a food item typically made by placing fillings like meat, cheese, or vegetables between two or more slices of bread or inside a split roll";
    }

    public function getIngredients(): array
    {
        $ingredients = [];
        $ingredients[] = $this->bread;
        array_merge($ingredients, $this->fillings);
        return array_merge($ingredients, $this->fillings);
    }
}