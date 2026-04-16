<?php

namespace App\Application\UseCases;

use App\Domain\Order\Order;
use App\Domain\Order\OrderInterface;
use App\Domain\Order\OrderItemModel;

class CreatorOrderUseCase
{
    public function execute(array $arOrderConfig): OrderInterface
    {
        $order = new Order();

        foreach ($arOrderConfig as $productName => $arSettings) {
            foreach ($arSettings as $arProductSetting) {
                $additionalIngredients = $arProductSetting['additional_ingredients'] ?: [];
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
}