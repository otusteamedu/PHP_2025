<?php

namespace App\Application\Services;

use App\Domain\Entities\Order;
use App\Domain\Entities\Product;
use App\Domain\Enums\OrderStatus;
use App\Domain\Observable\ProductSubject;
use App\Infrastructure\Templates\BurgerCookingTemplate;
use App\Infrastructure\Templates\SandwichCookingTemplate;
use App\Infrastructure\Templates\HotDogCookingTemplate;
use App\Domain\Enums\ProductType;

class KitchenService
{
    private ProductSubject $productSubject;
    private BurgerCookingTemplate $burgerCooking;
    private SandwichCookingTemplate $sandwichCooking;
    private HotDogCookingTemplate $hotDogCooking;

    public function __construct(
        ProductSubject $productSubject,
        BurgerCookingTemplate $burgerCooking,
        SandwichCookingTemplate $sandwichCooking,
        HotDogCookingTemplate $hotDogCooking
    ) {
        $this->productSubject = $productSubject;
        $this->burgerCooking = $burgerCooking;
        $this->sandwichCooking = $sandwichCooking;
        $this->hotDogCooking = $hotDogCooking;
    }

    public function prepareOrder(Order $order): Order
    {
        $order->setStatus(OrderStatus::PREPARING);
        
        foreach ($order->getProducts() as $product) {
            $this->prepareProduct($product);
        }
        
        $order->setStatus(OrderStatus::READY);
        
        return $order;
    }

    private function prepareProduct(Product $product): void
    {
        $cookingTemplate = match ($product->getType()) {
            ProductType::BURGER => $this->burgerCooking,
            ProductType::SANDWICH => $this->sandwichCooking,
            ProductType::HOTDOG => $this->hotDogCooking,
        };

        // Устанавливаем статус через Subject для уведомления наблюдателей
        $this->productSubject->setProductStatus($product, \App\Domain\Enums\ProductStatus::PREPARING);
        
        $cookingTemplate->cook($product);
        
        $this->productSubject->setProductStatus($product, $product->getStatus());
    }
}