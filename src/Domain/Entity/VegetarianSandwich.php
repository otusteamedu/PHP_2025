<?php

namespace App\Domain\Entity;

use App\Domain\Entity\Interface\RecipeAwareInterface;
use App\Domain\Entity\Interface\SandwichInterface;

class VegetarianSandwich extends Product implements SandwichInterface, RecipeAwareInterface
{
    public function __construct()
    {
        parent::__construct();
        $this->name = 'Vegetarian Sandwich';
    }

    public function getRecipe(): array
    {
        return ['Bread', 'Grass'];
    }
}
