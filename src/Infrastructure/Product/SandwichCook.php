<?php

namespace App\Infrastructure\Product;

use App\Application\Ingredient\BunIngredient;
use App\Application\Ingredient\CheeseIngredient;
use App\Application\Ingredient\SausageIngredient;
use App\Application\Product\ProductCookInterface;
use App\Domain\Entity\Product;
use App\Infrastructure\Builder\ProductBuilder;

class SandwichCook implements ProductCookInterface
{
    public function cook(): Product {
        return (new ProductBuilder()) // Builder \\
            ->setType('sandwich')
            ->setStatus('cooking')
            //  --- Composite --- \\
            ->addIngredient(new CheeseIngredient())
            ->addIngredient(new SausageIngredient())
            ->addIngredient(new BunIngredient())
            //  --- Composite --- \\
            ->build();
    }
}