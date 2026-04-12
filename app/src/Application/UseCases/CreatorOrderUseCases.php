<?php

namespace App\Application\UseCases;

use App\Domain\Ingredient\IngredientInterface;
use App\Domain\Order\Order;
use App\Domain\Order\OrderInterface;
use App\Domain\Order\OrderItemModel;
use App\Infrastructure\Ingredient\IngredientFactory;
use App\Infrastructure\Ingredient\IngredientLoader;

class CreatorOrderUseCases
{
    public function execute(array $arOrderConfig): OrderInterface
    {
        $order = new Order();

        foreach ($arOrderConfig as $productName => $arSettings) {
            foreach ($arSettings as $arProductSetting) {
                $additionalIngredients = $arProductSetting['additional_ingredients']
                    ? $this->getAdditionalIngredients($arProductSetting['additional_ingredients'])
                    : [];
                $orderItemModel = new OrderItemModel(
                    productName: $productName,
                    recipeType: $arProductSetting['type'],
                    count: $arProductSetting['count'],
                    additionalIngredients: $additionalIngredients
                );
                $order->addItem($orderItemModel);
            }
        }

        return $order;
    }

    /** @return IngredientInterface[] */
    private function getAdditionalIngredients(array $arIngredients): array
    {
        $ingredientsFactory = new IngredientFactory();
        $ingredientLoader = new IngredientLoader($ingredientsFactory);

        return $ingredientLoader->loadFromArray($arIngredients);
    }
}