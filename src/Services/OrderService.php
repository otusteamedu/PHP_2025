<?php declare(strict_types=1);

namespace Fastfood\Services;

use Exception;
use Fastfood\Factories\ProductFactory;
use Fastfood\Orders\Order;
use Fastfood\Orders\States;

class OrderService
{
    public function __construct(
        private ProductFactory $productFactory,
        private States\OrderStateHandler $stateHandler,
        private array $observers
    ) {}

    /**
     * @throws Exception
     */
    public function createOrder(array $productsData): Order
    {
        if (empty($productsData)) {
            throw new Exception("Заказ должен содержать хотя бы один продукт");
        }

        $order = new Order();

        // Подписываем наблюдателей
        foreach ($this->observers as $observer) {
            $order->attach($observer);
        }

        $order->setProductsData($productsData);

        // Запускаем цепочку обработки
        $this->stateHandler->process($order);

        return $order;
    }
}