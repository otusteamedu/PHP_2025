<?php

declare(strict_types=1);

namespace App\Core\Strategy;

use App\Core\Decorator\HotDogDecorator;
use App\Core\Factories\FastFoodFactory;

readonly class HotDogStrategy
{
    public function __construct(
        private FastFoodFactory $product,
        private array           $ingredients
    )
    {
        $this->cook();
    }

    /**
     * @return HotDogDecorator
     */
    public function cook(): HotDogDecorator
    {
        return new HotDogDecorator($this->product, $this->ingredients);
    }
}