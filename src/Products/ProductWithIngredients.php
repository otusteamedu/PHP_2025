<?php

declare(strict_types=1);

namespace App\Products;

class ProductWithIngredients extends AbstractProduct
{
    private array $_ingredients = [];
    private int $_ingredientsPrice = 0;
    
    public array $ingredients {
        get {
            return $this->_ingredients;
        }
    }
    
    public int $ingredientsPrice {
        get {
            return $this->_ingredientsPrice;
        }
    }

    public function addIngredient(string $ingredient, int $price): void
    {
        $this->_ingredients[] = $ingredient;
        $this->_ingredientsPrice += $price;
    }

    public function getTotalPrice(): int
    {
        return $this->price + $this->ingredientsPrice;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function getDescription(): string
    {
        $ingredientsList = empty($this->ingredients) ? 'без дополнительных ингредиентов' : 'с ' . implode(', ', $this->ingredients);
        return $this->description . ' (' . $ingredientsList . ')';
    }
}
