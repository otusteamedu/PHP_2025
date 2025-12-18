<?php

declare(strict_types=1);

namespace App\Domain\Entity\Food;

abstract class FoodAbstract implements FoodInterface
{
    protected string $name = "Unknown food";

    /** Базовые ингредиенты
     * @var array
     */
    protected array $baseIngredients = [];

    /** Ингридиенты
     * @var array
     */
    protected array $ingredients = [];

    /** Ингридиенты по рецепту
     * @var array
     */
    protected array $ingredientsRecipe = [];

    public function getName(): string
    {
        return $this->name;
    }

    public function getNameIngredients(): string
    {
        return \implode(', ', $this->ingredients);
    }

    public function getProductComposition(): string
    {
        return $this->getName() . ' with ingredients: ' . $this->getNameIngredients();
    }

    public function addIngredientsByRecipe(): void
    {
        $this->ingredients = \array_merge($this->baseIngredients, $this->ingredientsRecipe);
    }
}
