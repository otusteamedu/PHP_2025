<?php

namespace App\Domain\Entity;

use App\Domain\Entity\Interface\BurgerInterface;
use App\Domain\Entity\Interface\RecipeAwareInterface;

class ClassicBurger extends Product implements BurgerInterface, RecipeAwareInterface
{
    public function __construct()
    {
        parent::__construct();
        $this->name = 'Classic Burger';
    }

    public function getRecipe(): array
    {
        return ['Bun', 'Patty', 'Sauce'];
    }
}
