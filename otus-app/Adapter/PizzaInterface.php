<?php

declare(strict_types=1);

namespace App\Adapter;

use App\ProductInterface;

interface PizzaInterface
{
    public function getDescription(): string;

    public function getName(): string;

    public function getIngredientList(): array;
}
