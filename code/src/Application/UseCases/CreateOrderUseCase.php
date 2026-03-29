<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Application\DTO\CreateOrderRequest;
use App\Application\DTO\OrderResponse;
use App\Domain\Interfaces\OrderRepositoryInterface;
use App\Domain\Interfaces\OrderBuilderInterface;
use App\Domain\Product\Decorator\CheeseDecorator;
use App\Domain\Product\Decorator\LettuceDecorator;
use App\Domain\Product\Decorator\OnionDecorator;
use App\Domain\Product\Decorator\PepperDecorator;
use App\Domain\Product\Decorator\TomatoDecorator;
use App\Domain\Product\Factory\ProductFactory;
use App\Domain\Interfaces\ProductInterface;
use Throwable;

class CreateOrderUseCase
{
    private const ADDITION_DECORATORS = [
        'lettuce' => LettuceDecorator::class,
        'onion' => OnionDecorator::class,
        'pepper' => PepperDecorator::class,
        'cheese' => CheeseDecorator::class,
        'tomato' => TomatoDecorator::class,
    ];

    public function __construct(
        private ProductFactory $productFactory,
        private OrderBuilderInterface $orderBuilder,
        private OrderRepositoryInterface $orderRepository
    ) {}

    public function execute(CreateOrderRequest $request): OrderResponse
    {
        try {
            if (empty($request->items)) {
                return OrderResponse::error('Список товаров пуст');
            }

            $orderBuilder = $this->orderBuilder->create();

            foreach ($request->items as $item) {
                $productType = $item['product_type'] ?? '';
                $additions = $item['additions'] ?? [];

                if (empty($productType)) {
                    return OrderResponse::error('Тип продукта не указан');
                }

                $product = $this->productFactory->createProduct($productType);
                $product = $this->applyAdditions($product, $additions);

                $orderBuilder->addProduct($product);
            }

            $order = $orderBuilder->build();

            $this->orderRepository->save($order);

            return OrderResponse::success(
                order: $order,
                message: 'Заказ успешно создан'
            );
        } catch (Throwable $e) {
            return OrderResponse::error('Произошла ошибка: ' . $e->getMessage());
        }
    }

    private function applyAdditions(ProductInterface $product, array $additions): ProductInterface
    {
        foreach ($additions as $addition) {
            $addition = strtolower(trim($addition));

            if (isset(self::ADDITION_DECORATORS[$addition])) {
                $decoratorClass = self::ADDITION_DECORATORS[$addition];
                $product = new $decoratorClass($product);
            }
        }

        return $product;
    }
}
