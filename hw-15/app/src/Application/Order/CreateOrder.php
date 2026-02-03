<?php

declare(strict_types=1);

namespace App\Application\Order;

use App\Domain\Ingredient\Factory\IngredientDecoratorFactory;
use App\Domain\Order\Order;
use App\Domain\Order\OrderBuilder;
use App\Domain\Order\Pipeline\OrderPipeline;
use App\Domain\Product\Factory\ProductFactory;

readonly class CreateOrder
{
    public function __construct(
        private ProductFactory $productFactory,
        private IngredientDecoratorFactory $ingredientFactory,
        private OrderPipeline $pipeline,
    ) {
    }

    public function execute(CreateOrderCommand $command): Order
    {
        $product = $this->productFactory->create($command->name);
        $product = $this->ingredientFactory->addIngredient($product, $command->ingredients);

        $order = (new OrderBuilder())
            ->addProduct($product, $command->quantity)
            ->build();

        $handler = $this->pipeline->build();
        $handler->handle($order);

        return $order;
    }
}
