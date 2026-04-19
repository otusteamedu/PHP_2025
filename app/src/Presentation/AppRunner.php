<?php

namespace App\Presentation;

use App\Application\DTO\PrepareOrderResultDto;
use App\Application\UseCases\CreatorOrderUseCase;
use App\Application\UseCases\InitializeStorageUseCase;
use App\Application\UseCases\PrepareOrderUseCase;
use App\Domain\Ingredient\Storage\IngredientStorageInterface;
use App\Domain\Product\Builder\ProductBuilder;
use App\Domain\Product\Chain\AddIngredientsHandler;
use App\Domain\Product\Chain\CheckIngredientsHandler;
use App\Domain\Product\Chain\CookingHandler;
use App\Domain\Product\Strategy\ProductStrategyFactory;
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

    public function prepareOrder(array $arOrder): PrepareOrderResultDto
    {
        $creatorOrderUseCases = new CreatorOrderUseCase();
        $order = $creatorOrderUseCases->execute($arOrder);

        $productStrategyFactory = new ProductStrategyFactory();

        $chain = new CookingHandler();

        $chain->setNext(new CheckIngredientsHandler())
            ->setNext(new AddIngredientsHandler());

        $productBuilder = new ProductBuilder($chain, $this->storage);

        $prepareOrderUseCase = new PrepareOrderUseCase($productStrategyFactory, $productBuilder);

        return $prepareOrderUseCase->execute($order);
    }
}