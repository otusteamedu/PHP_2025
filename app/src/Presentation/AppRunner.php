<?php

namespace App\Presentation;

use App\Application\UseCases\CreatorOrderUseCases;
use App\Application\UseCases\InitializeStorageUseCase;
use App\Domain\Ingredient\Storage\IngredientStorageInterface;
use App\Infrastructure\Ingredient\IngredientFactory;
use App\Infrastructure\Ingredient\IngredientLoader;

class AppRunner
{
    protected IngredientStorageInterface $storage;
    public function __construct(string $storageConfigPath)
    {
        $ingredientsFactory = new IngredientFactory();
        $ingredientLoader = new IngredientLoader($ingredientsFactory);

        $useCase = new InitializeStorageUseCase($ingredientLoader);
        $this->storage = $useCase->execute($storageConfigPath);
    }

    public function prepareOrder(array $arOrder): void
    {
        $creatorOrderUseCases = new CreatorOrderUseCases();
        $order = $creatorOrderUseCases->execute($arOrder);

        // todo заказ передается на кухню и возвращается блюда + сообщения об ошибках (нехватка ингредиентов)
    }
}