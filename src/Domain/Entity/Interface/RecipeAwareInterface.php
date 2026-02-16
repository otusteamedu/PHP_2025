<?php

namespace App\Domain\Entity\Interface;

interface RecipeAwareInterface extends ProductInterface
{
    /** @return string[] */
    public function getRecipe(): array;
}
