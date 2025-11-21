<?php
declare(strict_types=1);

namespace Dinargab\Homework15\Builder\Director;

use Dinargab\Homework15\Builder\BuilderInterface;
use Dinargab\Homework15\Model\Product\ProductInterface;

class RecipesDirector
{
    private BuilderInterface $builder;


    public function prepareNormalProduct(): ProductInterface
    {
        $product = $this->builder
            ->addMainIngredients()
            ->addFilling("mayo")
            ->build();

        return $product;
    }

    public function preparePremiumProduct(): ProductInterface
    {
        $product = $this->builder
            ->addMainIngredients()
            ->addFilling("mayo")
            ->addFilling("gold")
            ->addFilling("truffle")
            ->build();

        return $product;
    }

    public function setBuilder(BuilderInterface $builder): void
    {
        $this->builder = $builder;
    }
}