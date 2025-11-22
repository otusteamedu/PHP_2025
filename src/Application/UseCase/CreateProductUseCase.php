<?php

namespace Blarkinov\Hw1500\Application\UseCase;

use Blarkinov\Hw1500\Application\Exception\FailedCookFoodExcepiton;
use Blarkinov\Hw1500\Domain\Fabric\FoodFabricInterface;
use Blarkinov\Hw1500\Domain\Strategies\CookingStrategyInterface;
use Blarkinov\Hw1500\Domain\ValueObject\Food\Burger\Burger;
use Blarkinov\Hw1500\Domain\ValueObject\Food\Food;
use Blarkinov\Hw1500\Domain\ValueObject\Food\Hotdog\Hotdog;
use Blarkinov\Hw1500\Domain\ValueObject\Food\Sandwich\Sandwich;

class CreateProductUseCase
{

    public function create(
        string $food,
        FoodFabricInterface $foodFabric,
        CookingStrategyInterface $cook,
    ): ?Food {

        if (strripos($food, basename(Burger::class)) !== false)
            $foodObject = $foodFabric->makeBurger();
        if (strripos($food, basename(Sandwich::class)) !== false)
            $foodObject = $foodFabric->makeSandwich();
        if (strripos($food, basename(Hotdog::class)) !== false)
            $foodObject = $foodFabric->makeHotdog();

        $foodObject->searchIngredients();
        $foodObject = $cook->cooking($foodObject);

        if (!$foodObject->checkStandart())
            return null;

        return $foodObject;
    }
}
