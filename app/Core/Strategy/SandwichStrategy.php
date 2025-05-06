<?php

declare(strict_types=1);

namespace App\Core\Strategy;

use App\Core\Decorator\SandwichDecorator;
use App\Core\Factories\FastFoodFactory;

readonly class SandwichStrategy implements CookingStrategyInterface
{
    public function __construct(
        private FastFoodFactory $product,
        private array           $ingredients
    )
    {
        $this->cook();
    }

    /**
     * @return SandwichDecorator
     */
    public function cook(): SandwichDecorator
    {
        return new SandwichDecorator($this->product, $this->ingredients);
    }
}