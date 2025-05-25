<?php

declare(strict_types=1);

namespace App\Domain\Entity\Food;

interface FoodInterface
{
    public function getName(): string;

    /** Получить имена ингредиентов
     * @return string
     */
    public function getNameIngredients(): string;

    /** Получить имя и состав продукта
     * @return string
     */
    public function getProductComposition(): string;

    /** Добавить ингредиенты по рецепту
     * @return void
     */
    public function addIngredientsByRecipe(): void;
}
