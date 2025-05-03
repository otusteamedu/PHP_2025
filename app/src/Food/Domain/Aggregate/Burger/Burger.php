<?php
declare(strict_types=1);


namespace App\Food\Domain\Aggregate\Burger;

use App\Food\Domain\Aggregate\Food;
use App\Food\Domain\Aggregate\VO\FoodTitle;

class Burger extends Food
{
    public function __construct(string $orderId, ?FoodTitle $title = null)
    {
        if (!$title) {
            $title = new FoodTitle('burger');
        }
        parent::__construct($title, $orderId);
        $this->add();
    }

    private function add(): void
    {
        $this->addIngredient(new BurgerBunIngredient());
        $this->addIngredient(new BurgerPuttyIngredient());
    }
}