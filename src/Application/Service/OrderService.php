<?php

namespace App\Application\Service;

use App\Application\Proxy\CookingServiceInterface;
use App\Application\Strategy\CookStrategyResolver;
use App\Domain\Entity\Interface\ProductInterface;

class OrderService
{
    private FoodFactoryProvider $factoryProvider;

    private CookStrategyResolver $strategyResolver;

    private ProductAssembler $assembler;

    private CookingServiceInterface $cookingService;

    public function __construct(
        FoodFactoryProvider $factoryProvider,
        CookStrategyResolver $strategyResolver,
        ProductAssembler $assembler,
        CookingServiceInterface $cookingService,
    ) {
        $this->factoryProvider = $factoryProvider;
        $this->strategyResolver = $strategyResolver;
        $this->assembler = $assembler;
        $this->cookingService = $cookingService;
    }

    public function placeOrder(Order $order): ProductInterface
    {
        $factory = $this->factoryProvider->forOrder($order);
        $strategy = $this->strategyResolver->resolve($order->type);

        $baseProduct = $strategy->createBaseProduct($factory);
        $product = $this->assembler->assemble($baseProduct, $order->optional);

        $this->cookingService->cook($product);

        return $product;
    }
}
