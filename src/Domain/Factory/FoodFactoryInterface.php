<?php

declare(strict_types=1);

namespace App\Domain\Factory;

interface FoodFactoryInterface
{
    public function createFood(string $foodName);
}
