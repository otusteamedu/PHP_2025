<?php

declare(strict_types=1);

namespace App\Repository;

use App\Models\Order;
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
            $order->setProductId($orderData['product_id']);
            $order->setStatus($orderData['status']);
            $order->setIngredients(json_decode($orderData['ingredients']));
            $order->setProductName($orderData['product_name']);
            $order->setProductPrice($orderData['product_price']);
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
        $sql = "INSERT INTO `orders` (`client_name`, `client_phone`, `product_id`, `status`, `ingredients`, `product_name`, `product_price`) 
                VALUES (:client_name, :client_phone, :product_id, :status, :ingredients, :product_name, :product_price)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':client_name' => $order->getClientName(),
            ':client_phone' => $order->getClientPhone(),
            ':product_id' => $order->getProductId(),
            ':status' => $order->getStatus(),
            ':ingredients' => json_encode($order->getIngredients()),
            ':product_name' => $order->getProductName(),
            ':product_price' => $order->getProductPrice()
        ]);
        $order->setId(intval($this->pdo->lastInsertId()));
        $order->setPaymentLink("http://fast_food_restaurant/orders/{$order->getId()}/pay");

        return $order;
    }

    /**
     * @param int $id
     * @return array
     */
    public function findOrder(int $id): array
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
        $sql = "UPDATE `orders` SET `status` = :status WHERE `id` = :id";
        $stmt = $this->pdo->prepare($sql);
        if ($stmt->execute([':id' => $order_id, ':status' => $status])) {
            return true;
        }

        return false;
    }
}