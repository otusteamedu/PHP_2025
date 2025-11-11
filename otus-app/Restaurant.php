<?php

declare(strict_types=1);

namespace App;

use App\Adapter\Nuggets;
use App\Adapter\Pizza;
use App\Adapter\PizzaAdapter;
use App\Builder\Hotdog;
use App\Builder\HotdogBuilderInterface;
use App\Iterator\PizzaWithStatus;
use App\Proxy\PizzaProxy;

class Restaurant
{
    public function __construct(
        private HotdogBuilderInterface $hotdogBuilder,
    ) {
    }

    public function setHotdogBuilder(HotdogBuilderInterface $class): void
    {
        $this->hotdogBuilder = $class;
    }

    public function buildHotdog(?string $name = null, array $ingredientList = []): Hotdog
    {
        $this->hotdogBuilder->setName($name);
        $this->hotdogBuilder->setIngredientList($ingredientList);

        return $this->hotdogBuilder->build();
    }

    public function getProductCustomDescription(?string $name = null, array $ingredientList = []): string
    {
        $class = match ($name) {
            'pizza' => new PizzaAdapter(new Pizza($name, $ingredientList)),
            'nuggets' => new Nuggets($name),
        };

        return $class->getCustomDescription();
    }

    public function buildPizza(?string $name = null, array $ingredientList = []): ?Pizza
    {
        $pizza = new PizzaProxy($name, $ingredientList);

        if ($pizza->checkIngredients() === false) {
            return null;
        }

        return $pizza;
    }

    public function buildPizzaWithNoCheck(?string $name = null, array $ingredientList = []): ?Pizza
    {
        return new PizzaProxy($name, $ingredientList);
    }

    public function buildPizzaWithStatus(?string $name = null, array $ingredientList = []): PizzaWithStatus
    {
        return new PizzaWithStatus($name, $ingredientList);
    }
}
