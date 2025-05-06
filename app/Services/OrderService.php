<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Factories\FastFoodFactory;
use App\Core\Observer\OrderNotifier;
use App\Core\Strategy\BurgerStrategy;
use App\Core\Strategy\HotDogStrategy;
use App\Core\Strategy\SandwichStrategy;
use App\Models\Order;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;

readonly class OrderService
{
    public function __construct(
        private OrderRepository $orderRepository,
        private ProductRepository        $productRepository,
        private OrderNotifier            $notifier,
    )
    {}

    /**
     * @return array
     */
    public function getList(): array
    {
        return $this->orderRepository->getOrderList();
    }

    public function getOrder(int $order_id): Order|false
    {
        $orderData = $this->orderRepository->findOrder($order_id);
        if ($orderData) {
            $order = $this->newOrder($orderData);
            if ($orderData['status'] === 'created') {
                $order->setPaymentLink("http://fast_food_restaurant/orders/{$orderData['id']}/pay");
            }

            return $order;
        }

        return false;
    }

    /**
     * @param array $orderData
     * @return Order
     */
    public function createOrder(array $orderData): Order
    {
        $order = $this->newOrder($orderData);

        $productData = $this->productRepository->getProductById(intval($order->getProductId()));

        $order_product = new FastFoodFactory($productData);

        switch ($order_product->getName()) {
            case 'Burger':
                $strategy = new BurgerStrategy($order_product, $orderData['ingredients'] ?? []);
                $product = $strategy->cook();
                $order->setProductName($product->getName());
                $order->setProductPrice($product->getPrice());
                break;
            case 'Sandwich':
                $strategy = new SandwichStrategy($order_product, $orderData['ingredients'] ?? []);
                $product = $strategy->cook();
                $order->setProductName($product->getName());
                $order->setProductPrice($product->getPrice());
                break;
            case 'Hot_dog':
                $strategy = new HotDogStrategy($order_product, $orderData['ingredients'] ?? []);
                $product = $strategy->cook();
                $order->setProductName($product->getName());
                $order->setProductPrice($product->getPrice());
            break;
            default:
                $order->setProductName($order_product->getName());
                $order->setProductPrice($order_product->getPrice());
        }

        return $this->orderRepository->saveOrder($order);
    }

    /**
     * @param int $order_id
     * @param int $payment
     * @return bool|string
     */
    public function payOrder(int $order_id, int $payment): bool|string
    {
        $orderData = $this->orderRepository->findOrder($order_id);

        if ($orderData['status'] != 'created') {
            return 'Оплата была проведена ранее';
        }

        $order = $this->newOrder($orderData);

        if ($order->getProductPrice() === (float)$payment) {
            if ($this->orderRepository->updateOrderStatus($order_id, 'at work')) {
                $order->setStatus('at work');
                $this->notifier->notify($order);
                return true;
            }
        }

        return false;
    }

    /**
     * @param array $orderData
     * @return Order
     */
    public function newOrder(array $orderData): Order
    {
        $order = new Order();
        $order->setId($orderData['id'] ?? null);
        $order->setClientName($orderData['client_name'] ?? null);
        $order->setClientPhone($orderData['client_phone'] ?? null);
        $order->setProductId($orderData['product_id'] ?? null);
        $order->setStatus($orderData['status'] ?? 'created');
        $order->setIngredients((array)$orderData['ingredients']) ?? [];
        $order->setProductName($orderData['product_name'] ?? null);
        $order->setProductPrice($orderData['product_price'] ?? null);

        return $order;
    }
}
