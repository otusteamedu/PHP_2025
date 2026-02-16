<?php

namespace App\Domain\Entity;

use App\Domain\Entity\Interface\BurgerInterface;
use App\Domain\Entity\Interface\RecipeAwareInterface;

class VegetarianBurger extends Product implements BurgerInterface, RecipeAwareInterface
{
    public function __construct()
    {
        parent::__construct();
        $this->name = 'Vegetarian Burger';
    }

    public function getRecipe(): array
    {
        return ['Bun', 'Grass', 'Sauce'];
    }
}
