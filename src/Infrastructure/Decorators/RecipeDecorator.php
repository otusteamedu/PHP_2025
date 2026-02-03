<?php

namespace App\Infrastructure\Decorators;

use App\Domain\Interfaces\ProductDecoratorInterface;
use App\Domain\Entities\Product;
use App\Domain\Enums\ProductType;

class RecipeDecorator implements ProductDecoratorInterface
{
    
    private array $recipes = [
        ProductType::BURGER->value => ['Салат', 'Помидор', 'Соус'],
        ProductType::SANDWICH->value => ['Салат', 'Майонез'],
        ProductType::HOTDOG->value => ['Кетчуп', 'Горчица', 'Лук'],
    ];

    public function decorate(Product $product, array $additionalIngredients = []): Product
    {
        $recipeIngredients = $this->recipes[$product->getType()->value] ?? [];
        
        foreach ($recipeIngredients as $ingredient) {
            $product->addIngredient($ingredient);
        }
        
        foreach ($additionalIngredients as $ingredient) {
            $product->addIngredient($ingredient);
        }
        
        return $product;
    }
}