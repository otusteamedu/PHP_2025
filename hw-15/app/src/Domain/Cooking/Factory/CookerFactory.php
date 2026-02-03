<?php

declare(strict_types=1);

namespace App\Domain\Cooking\Factory;

use App\Domain\Cooking\BurgerCooker;
use App\Domain\Cooking\HotDogCooker;
use App\Domain\Cooking\ProductCookerInterface;
use App\Domain\Cooking\SandwichCooker;
use App\Domain\Product\Burger;
use App\Domain\Product\Exception\InvalidProductException;
use App\Domain\Product\HotDog;
use App\Domain\Product\ProductInterface;
use App\Domain\Product\Sandwich;

class CookerFactory
{
    public function create(ProductInterface $product): ProductCookerInterface
    {
        return match (get_class($product)) {
            Burger::class => new BurgerCooker(),
            Sandwich::class => new SandwichCooker(),
            HotDog::class => new HotDogCooker(),
            default => throw new InvalidProductException($product->getName()),
        };
    }
}

