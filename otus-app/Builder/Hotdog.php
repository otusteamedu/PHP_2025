<?php

declare(strict_types=1);

namespace App\Builder;

use App\ProductInterface;

class Hotdog implements ProductInterface
{
    public string $name;
    public array $ingredientList;

    public function __construct(
        HotdogBuilderInterface $builder,
    ) {
        $this->name = $builder->getName();
        $this->ingredientList = $builder->getIngredientList();
    }
}
