<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Entity\Food\FoodDecoratorInterface;
use App\Domain\Entity\Food\FoodInterface;
use App\Domain\Observer\EventInterface;

class FoodDecorator implements FoodInterface, FoodDecoratorInterface, EventInterface
{
    protected FoodInterface $food;

    private string $cookingStatus = '';

    /** Ингридиенты по пожеланию клиента
     * @var array
     */
    protected array $userIngredients = [];

    public function __construct(FoodInterface $food)
    {
        $this->food = $food;
    }

    public function getName(): string
    {
        return $this->food->getName();
    }

    public function getNameIngredients(): string
    {
        return $this->food->getNameIngredients();
    }

    public function getProductComposition(): string
    {
        return $this->food->getProductComposition() . ', ' . \implode(', ', $this->userIngredients);
    }

    public function addIngredientsByRecipe(): void
    {
        $this->food->addIngredientsByRecipe();
    }

    /** Добавить ингредиент по желанию клиента
     * @param string $userIngredient
     * @return void
     */
    public function setClientIngredient(string $userIngredient): void
    {
        $this->userIngredients[] = $userIngredient;
    }

    public function getCookingStatus(): string
    {
        return $this->cookingStatus;
    }

    public function setCookingStatus(string $cookingStatus): void
    {
        $this->cookingStatus = $cookingStatus;
    }
}
