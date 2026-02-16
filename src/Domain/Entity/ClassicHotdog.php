<?php

namespace App\Domain\Entity;

use App\Domain\Entity\Interface\HotdogInterface;
use App\Domain\Entity\Interface\RecipeAwareInterface;

class ClassicHotdog extends Product implements HotdogInterface, RecipeAwareInterface
{
    public function __construct()
    {
        parent::__construct();
        $this->name = 'Classic Hotdog';
    }

    public function getRecipe(): array
    {
        return ['Bun', 'Sausage', 'Mustard'];
    }
}
