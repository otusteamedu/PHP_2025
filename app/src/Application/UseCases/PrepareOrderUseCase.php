<?php

namespace App\Application\UseCases;

use App\Application\DTO\PrepareOrderResultDto;
use App\Domain\Order\OrderInterface;
use App\Domain\Order\OrderItemModelInterface;
use App\Domain\Product\Builder\ProductBuilderInterface;
use App\Domain\Product\ProductInterface;
use App\Domain\Product\Strategy\ProductStrategyFactoryInterface;
use App\Domain\Product\Strategy\ProductStrategyInterface;

class PrepareOrderUseCase
{
    public function __construct(
        protected ProductStrategyFactoryInterface $strategyFactory,
        protected ProductBuilderInterface $productBuilder
    )
    {
    }

    public function execute(OrderInterface $order): PrepareOrderResultDto
    {
        $dto = new PrepareOrderResultDto();

        $orderItemModels = $order->getItems();

        foreach ($orderItemModels as $orderItemModel) {
            try {
                $dto->addProduct($this->cook($orderItemModel));
            } catch (\Throwable $e) {
                $dto->addError($e->getMessage());
            }
        }

        return $dto;
    }
    private function cook(OrderItemModelInterface $order): ProductInterface
    {
        /** @var ProductStrategyInterface $productPreparation */
        $productPreparation = $this->strategyFactory->create($order->getProductName());

        $product = $productPreparation->create();

        $product = $this->productBuilder->build(
            $product,
            $productPreparation->getBaseIngredients($order->getRecipeType()),
            $order->getAdditionalIngredients()
        );

        return $product;
    }
}