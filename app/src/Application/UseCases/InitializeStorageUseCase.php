<?php

namespace App\Application\UseCases;

use App\Domain\Ingredient\Storage\IngredientStorage;
use App\Domain\Ingredient\Storage\IngredientStorageInterface;
use App\Infrastructure\Ingredient\IngredientLoader;

class InitializeStorageUseCase
{
    public function __construct(
        private readonly IngredientLoader $loader
    )
    {

    }

    public function execute(string $path): IngredientStorageInterface
    {
        $ingredients = $this->loader->loadFromFile($path);

        return new IngredientStorage($ingredients);
    }
}