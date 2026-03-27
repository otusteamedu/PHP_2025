<?php

declare(strict_types=1);

namespace App\Adapter;

use App\ProductInterface;

interface NuggetsInterface
{
    public function getCustomDescription(): string;

    public function getName(): string;

    public function getIngredientList(): array;
}
