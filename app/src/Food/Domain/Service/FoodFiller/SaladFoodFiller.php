<?php

declare(strict_types=1);

namespace App\Food\Domain\Service\FoodFiller;

use App\Food\Domain\Aggregate\FoodInterface;
use App\Food\Domain\Aggregate\Ingredient\SaladIngredient;

readonly class SaladFoodFiller extends BaseFoodFiller
{
    public function __construct(private FoodInterface $food)
    {
        $this->food->addIngredient(new SaladIngredient());
        parent::__construct($this->food);
    }
}
