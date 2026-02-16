<?php

namespace App\Application\Service;

use App\Application\Factory\FoodFactoryInterface;

class FoodFactoryProvider
{
    private FoodFactoryInterface $classicFactory;

    private FoodFactoryInterface $vegetarianFactory;

    public function __construct(
        FoodFactoryInterface $classicFactory,
        FoodFactoryInterface $vegetarianFactory,
    ) {
        $this->classicFactory = $classicFactory;
        $this->vegetarianFactory = $vegetarianFactory;
    }

    public function forOrder(Order $order): FoodFactoryInterface
    {
        return $order->vegetarian ? $this->vegetarianFactory : $this->classicFactory;
    }
}
