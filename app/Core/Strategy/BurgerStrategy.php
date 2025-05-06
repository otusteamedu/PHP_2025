<?php

declare(strict_types=1);

namespace App\Core\Strategy;

use App\Core\Decorator\BurgerDecorator;
use App\Core\Factories\FastFoodFactory;

readonly class BurgerStrategy implements CookingStrategyInterface
{
    public function __construct(
        private FastFoodFactory $product,
        private array           $ingredients
    )
    {
        $this->cook();
    }

    /**
     * @return BurgerDecorator
     */
    public function cook(): BurgerDecorator
    {
       return new BurgerDecorator($this->product, $this->ingredients);
    }
}