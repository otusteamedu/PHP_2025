<?php

declare(strict_types=1);

namespace Infrastructure\Repository;

use Domain\Models\Order;
use PDO;

class OrderRepository extends BaseRepository
{
    /**
     * @return array
     */
    public function getOrderList(): array
    {
        $sql = "SELECT * FROM `orders`";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $orders = [];

        while ($orderData = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $order = new Order();
            $order->setId($orderData['id']);
            $order->setClientName($orderData['client_name']);
            $order->setClientPhone($orderData['client_phone']);
            $order->setStatus($orderData['order_status']);
            $order->setIngredients(json_decode($orderData['ingredients']));
            $order->setProduct($orderData['product']);
            $order->setPrice($orderData['price']);
            $orders[] = $order;
        }

        return $orders;
    }

    /**
     * @param Order $order
     * @return Order
     */
    public function saveOrder(Order $order): Order
    {
        $sql = "INSERT INTO `orders` (`client_name`, `client_phone`, `order_status`, `ingredients`, `product`, `price`) 
                VALUES (:client_name, :client_phone, :order_status, :ingredients, :product, :price)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':client_name' => $order->getClientName(),
            ':client_phone' => $order->getClientPhone(),
            ':order_status' => $order->getStatus(),
            ':ingredients' => json_encode($order->getIngredients()),
            ':product' => $order->getProduct(),
            ':price' => $order->getProductPrice()
        ]);
        $order->setId(intval($this->pdo->lastInsertId()));
        $order->setPaymentLink("http://fast_food_restaurant/orders/{$order->getId()}/pay");

        return $order;
    }

    /**
     * @param int $id
     * @return array|false
     */
    public function findOrder(int $id): array|false
    {
        $sql = "SELECT * FROM `orders` WHERE `id` = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @param int $order_id
     * @param string $status
     * @return bool
     */
    public function updateOrderStatus(int $order_id, string $status): bool
    {
        $sql = "UPDATE `orders` SET `order_status` = :order_status WHERE `id` = :id";
        $stmt = $this->pdo->prepare($sql);
        if ($stmt->execute([':id' => $order_id, ':order_status' => $status])) {
            return true;
        }

        return false;
    }
}

