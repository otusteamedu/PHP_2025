<?php

declare(strict_types=1);

namespace Infrastructure\Services;

use App\Observer\OrderNotifier;
use Domain\Factories\FastFoodFactory;
use Domain\Models\Order;
use Domain\Strategy\ProductStrategy;
use Infrastructure\Repository\OrderRepository;

readonly class OrderService
{
    public function __construct(
        private OrderRepository $orderRepository,
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
            if ($orderData['order_status'] === 'created') {
                $order->setPaymentLink("http://fast_food_restaurant/orders/{$orderData['id']}/pay");
            }

            return $order;
        }

        return false;
    }

    /**
     * @param array $orderData
     * @return Order|false
     */
    public function createOrder(array $orderData): Order|false
    {
        $order = $this->newOrder($orderData);

        $base_product = FastFoodFactory::createProduct($order->getProduct());

        $strategy = new ProductStrategy($base_product, $orderData['ingredients'] ?? []);
        $product = $strategy->cook();
        $order->setProduct($product->getName());
        $order->setPrice($product->getPrice());

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

        if (!$orderData) {
            return 'Заказ не найден.';
        }

        if ($orderData['order_status'] != 'created') {
            return 'Оплата была проведена ранее.';
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
        $order->setStatus($orderData['order_status'] ?? 'created');
        $order->setIngredients((array)$orderData['ingredients']) ?? [];
        $order->setProduct($orderData['product'] ?? null);
        $order->setPrice($orderData['price'] ?? null);

        return $order;
    }
}
