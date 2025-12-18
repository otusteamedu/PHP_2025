<?php

declare(strict_types=1);

namespace App\Domain\Entity\Food;

interface FoodDecoratorInterface
{
    public function addIngredientsByRecipe(): void;

    /** Добавить ингредиент по желанию клиента
     * @param string $userIngredient
     * @return void
     */
    public function setClientIngredient(string $userIngredient): void;

    public function getCookingStatus(): string;

    public function setCookingStatus(string $cookingStatus): void;
}
