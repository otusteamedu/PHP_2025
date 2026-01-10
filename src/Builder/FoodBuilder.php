<?php declare(strict_types=1);

namespace App\Builder;

use App\Core\FoodProductInterface;

class FoodBuilder
{
    private FoodProductInterface $product;

    public function setBase(FoodProductInterface $product): void
    {
        $this->product = $product;
    }

    public function addIngredient(callable $decorator): void
    {
        $this->product = $decorator($this->product);
    }

    public function getProduct(): FoodProductInterface
    {
        return $this->product;
    }
}
