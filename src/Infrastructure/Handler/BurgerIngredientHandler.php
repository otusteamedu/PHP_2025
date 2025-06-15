<?php

namespace App\Infrastructure\Handler;

use App\Application\Product\ProductHandler;
use App\Domain\Entity\Product;
use App\Infrastructure\Product\Task\CountProductIngredientTask;
use InvalidArgumentException;

class BurgerIngredientHandler extends ProductHandler
{
    public function handle(Product $product): void {
        $countedIngredients = (new CountProductIngredientTask())->run($product);

        if (empty($countedIngredients['bun']) || $countedIngredients['bun'] < 2) {
            throw new InvalidArgumentException("Булочек должно быть как минимум 2");
        }

        if (empty($countedIngredients['cutlet']) || $countedIngredients['cutlet'] < 1) {
            throw new InvalidArgumentException("Котлет должно быть как минимум 1");
        }

        if (empty($countedIngredients['salat']) || $countedIngredients['salat'] < 1) {
            throw new InvalidArgumentException("Салат должно быть как минимум 1");
        }

        parent::handle($product);
    }
}