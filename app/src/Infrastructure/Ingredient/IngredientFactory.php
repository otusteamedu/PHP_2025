<?php

namespace App\Infrastructure\Ingredient;

use App\Domain\Ingredient\IngredientInterface;
use App\Domain\Ingredient\Onion;
use App\Domain\Ingredient\Cheese;
use App\Domain\Ingredient\PizzaCrust;
use App\Domain\Ingredient\Ketchup;
use App\Domain\Ingredient\Cutlet;
use App\Domain\Ingredient\Salad;
use App\Domain\Ingredient\Mayonnaise;
use App\Domain\Ingredient\Tomato;
use App\Domain\Ingredient\Mustard;
use App\Domain\Ingredient\Sausage;
use App\Domain\Ingredient\HotDogBun;
use App\Domain\Ingredient\FriedOnions;

class IngredientFactory
{
    public function create(string $name, int $count): IngredientInterface
    {
        if (!isset($this->getMap()[$name])) {
            throw new \RuntimeException("Unknown ingredient {$name}");
        }

        $class = $this->getMap()[$name];

        return new $class($count);
    }

    /**
     * @return IngredientInterface[]
     */
    protected function getMap(): array
    {
        return [
            'onion' => Onion::class,
            'cheese' => Cheese::class,
            'pizza-crust' => PizzaCrust::class,
            'ketchup' => Ketchup::class,
            'cutlet' => Cutlet::class,
            'salad' => Salad::class,
            'mayonnaise' => Mayonnaise::class,
            'tomato' => Tomato::class,
            'mustard' => Mustard::class,
            'sausage' => Sausage::class,
            'hot-dog-bun' => HotDogBun::class,
            'fried-onions' => FriedOnions::class,
        ];
    }
}