<?php
namespace App\Cooking;

use App\Product\Food;

abstract class AbstractCookingProcess implements CookingProcess {
    protected Food $food;

    public function __construct(Food $food) {
        $this->food = $food;
    }

    public function process(): void {
        $this->checkIngredients();
        echo "Приготовление '{$this->food->getName()}'..." . PHP_EOL;
        echo "Ингредиенты: " . $this->food->prepare() . PHP_EOL;
        $this->qualityControl();
    }

    abstract protected function checkIngredients(): void;
    abstract protected function qualityControl(): void;
}
