<?php

namespace App\Infrastructure\Product;

use App\Application\Ingredient\BunIngredient;
use App\Application\Ingredient\KetchupIngredient;
use App\Application\Ingredient\SausageIngredient;
use App\Application\Product\ProductCookInterface;
use App\Domain\Entity\Product;
use App\Infrastructure\Builder\ProductBuilder;

class HotDogCook implements ProductCookInterface
{
    public function cook(): Product {
        return (new ProductBuilder()) // Builder \\
            ->setType('hotdog')
            ->setStatus('cooking')
            //  --- Composite --- \\
            ->addIngredient(new KetchupIngredient())
            ->addIngredient(new SausageIngredient())
            ->addIngredient(new BunIngredient())
            //  --- Composite --- \\
            ->build();
    }
}