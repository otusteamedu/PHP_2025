<?php

declare(strict_types=1);

namespace App\Food\Domain\Service\FoodFiller;

use App\Food\Domain\Aggregate\FoodInterface;
use App\Food\Domain\Aggregate\Ingredient\OnionIngredient;

readonly class OnionFoodFiller extends BaseFoodFiller
{
    public function __construct(private FoodInterface $food)
    {
        $this->food->addIngredient(new OnionIngredient());
        parent::__construct($this->food);
    }
}
