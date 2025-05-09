<?php
declare(strict_types=1);

namespace App\Food\Domain\Aggregate\Burger;

use App\Food\Domain\Aggregate\Food;
use App\Food\Domain\Aggregate\FoodType;
use App\Food\Domain\Aggregate\VO\FoodTitle;
use App\Shared\Application\Publisher\PublisherInterface;

class Burger extends Food
{
    public function __construct(string $orderId, private readonly PublisherInterface $publisher, ?FoodTitle $title = null)
    {
        if (!$title) {
            $title = new FoodTitle('burger');
        }

        parent::__construct($title, $orderId, FoodType::BURGER, $this->publisher);
        $this->add();
    }

    private function add(): void
    {
        $this->addIngredient(new BurgerBunIngredient());
        $this->addIngredient(new BurgerPuttyIngredient());
    }

}