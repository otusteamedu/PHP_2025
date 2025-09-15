<?php declare(strict_types=1);

namespace Fastfood\Orders\States;

use Exception;
use Fastfood\Factories\ProductFactory;
use Fastfood\Orders\Order;

class NewOrderHandler extends OrderStateHandler
{

    public function __construct(private ProductFactory $productFactory)
    {
    }

    /**
     * @throws Exception
     */
    public function process(Order $order): void
    {
        if ($order->getStatus() === 'new') {
            $order = $this->getOrderCost($order);
            $order->setStatus('accepted');
            $order->notify('order_accepted');
        }

        $this->handle($order);
    }

    /**
     * @throws Exception
     */
    private function getOrderCost(Order $order): Order
    {
        $totalCost = 0.0;
        $productsData = $order->getProductsData();

        foreach ($productsData as $productData) {
            if (!isset($productData['type'])) {
                throw new Exception("Тип продукта не указан");
            }

            $totalCost += $this->productFactory->calculateProductCost($productData['type'], $productData['ingredients'] ?? null);
        }

        $order->setTotalCost($totalCost);

        return $order;
    }

}