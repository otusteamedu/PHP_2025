<?php

declare(strict_types=1);

namespace App\Core\Factories;

interface FastFoodFactoryInterface
{
    public function getName(): string;

    public function getPrice(): float;
}