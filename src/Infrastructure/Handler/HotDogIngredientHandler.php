<?php

namespace App\Infrastructure\Handler;

use App\Application\Product\ProductHandler;
use App\Domain\Entity\Product;
use App\Infrastructure\Product\Task\CountProductIngredientTask;
use InvalidArgumentException;

class HotDogIngredientHandler extends ProductHandler
{
    public function handle(Product $product): void {
        $countedIngredients = (new CountProductIngredientTask())->run($product);

        if (empty($countedIngredients['bun']) || $countedIngredients['bun'] < 1) {
            throw new InvalidArgumentException("Булочек должно быть как минимум 1");
        }

        if (empty($countedIngredients['sausage']) || $countedIngredients['sausage'] < 1) {
            throw new InvalidArgumentException("Сосисок должно быть как минимум 1");
        }

        if (empty($countedIngredients['ketchup']) || $countedIngredients['ketchup'] < 1) {
            throw new InvalidArgumentException("Кетчуп должно быть как минимум 1 порция");
        }

        parent::handle($product);
    }
}