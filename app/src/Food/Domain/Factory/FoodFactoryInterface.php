<?php
declare(strict_types=1);


namespace App\Food\Domain\Factory;

use App\Food\Domain\Aggregate\Food;

interface FoodFactoryInterface
{
    public function make(string $title): Food;
}