<?php

declare(strict_types=1);

namespace Otus\Code\Application\Product\Recipe;

use Otus\Code\Application\Product\Decorator\CheeseDecorator;
use Otus\Code\Application\Product\Decorator\LettuceDecorator;
use Otus\Code\Application\Product\Decorator\OnionDecorator;
use Otus\Code\Application\Product\Decorator\PepperDecorator;
use Otus\Code\Application\Product\Decorator\PicklesDecorator;

class RecipeBook
{
    public static function classicBurger(): Recipe
    {
        return new Recipe('Classic Burger', [
            CheeseDecorator::class,
            LettuceDecorator::class,
            OnionDecorator::class,
        ]);
    }

    public static function spicyDog(): Recipe
    {
        return new Recipe('Spicy Dog', [
            OnionDecorator::class,
            PepperDecorator::class,
        ]);
    }
}
