<?php declare(strict_types=1);

namespace Fastfood\Orders\States;

use Exception;
use Fastfood\Factories\ProductFactory;
use Fastfood\Orders\Order;

class AcceptedHandler extends OrderStateHandler
{

    public function __construct(private ProductFactory $productFactory)
    {
    }

    /**
     * @throws Exception
     */
    public function process(Order $order): void
    {
        if ($order->getStatus() === 'accepted') {
            $order->notify('order_preparing');
            $this->createProductsForOrder($order); // Начинаем готовить заказ
            $order->setStatus('preparing');
        }

        $this->handle($order);
    }

    /**
     * @throws Exception
     */
    private function createProductsForOrder(Order $order): void
    {
        $productsData = $order->getProductsData();

        foreach ($productsData as $productData) {
            if (!isset($productData['type'])) {
                throw new Exception("Тип продукта не указан");
            }

            $product = match ($productData['type']) {
                'burger' => $this->productFactory->createBurger($productData['ingredients'] ?? null),
                'sandwich' => $this->productFactory->createSandwich($productData['ingredients'] ?? null),
                'hotdog' => $this->productFactory->createHotdog($productData['ingredients'] ?? null),
                default => throw new Exception("Неизвестный тип продукта: " . $productData['type'])
            };
            $order->addProduct($product);
        }

    }

}