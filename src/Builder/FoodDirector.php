<?php declare(strict_types=1);

namespace App\Builder;

use App\Core\FoodProductInterface;
use App\Decorator\CheeseDecorator;
use App\Decorator\OnionDecorator;
use App\Strategy\FoodStrategyInterface;

class FoodDirector
{
    public function buildClassicCheeseBurger(FoodBuilder $builder, FoodStrategyInterface $strategy): FoodProductInterface
    {
        $builder->setBase($strategy->createProduct());
        $builder->addIngredient(fn($p) => new CheeseDecorator($p));
        $builder->addIngredient(fn($p) => new OnionDecorator($p));
        return $builder->getProduct();
    }
}
