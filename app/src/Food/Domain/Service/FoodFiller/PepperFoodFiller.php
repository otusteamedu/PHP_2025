<?php

declare(strict_types=1);

namespace App\Food\Domain\Service\FoodFiller;

use App\Food\Domain\Aggregate\FoodInterface;
use App\Food\Domain\Aggregate\Ingredient\PepperIngredient;

readonly class PepperFoodFiller extends BaseFoodFiller
{
    public function __construct(private FoodInterface $food)
    {
        $this->food->addIngredient(new PepperIngredient());
        parent::__construct($this->food);
    }
}
