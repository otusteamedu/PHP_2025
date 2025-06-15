<?php

namespace App\Infrastructure\Product;

use App\Application\Ingredient\BunIngredient;
use App\Application\Ingredient\CutletIngredient;
use App\Application\Ingredient\KetchupIngredient;
use App\Application\Ingredient\OnionIngredient;
use App\Application\Ingredient\SalatIngredient;
use App\Application\Product\ProductCookInterface;
use App\Domain\Entity\Product;
use App\Infrastructure\Builder\ProductBuilder;

class BurgerCook implements ProductCookInterface
{
    public function cook(): Product {
        return (new ProductBuilder()) // Builder \\
            ->setType('burger')
            ->setStatus('cooking')
            //  --- Composite --- \\
            ->addIngredient(new BunIngredient())
            ->addIngredient(new OnionIngredient())
            ->addIngredient(new SalatIngredient())
            ->addIngredient(new CutletIngredient())
            ->addIngredient(new KetchupIngredient())
            ->addIngredient(new BunIngredient())
            //  --- Composite --- \\
            ->build();
    }
}