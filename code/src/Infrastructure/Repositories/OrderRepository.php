<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Order;
use App\Domain\Enums\OrderStatus;
use App\Domain\Interfaces\OrderRepositoryInterface;
use App\Domain\Interfaces\ProductInterface;
use App\Domain\Product\Decorator\CheeseDecorator;
use App\Domain\Product\Decorator\LettuceDecorator;
use App\Domain\Product\Decorator\OnionDecorator;
use App\Domain\Product\Decorator\PepperDecorator;
use App\Domain\Product\Decorator\TomatoDecorator;
use App\Domain\Product\Factory\ProductFactory;
use PDO;
use ReflectionClass;
use Exception;
use DateTimeImmutable;

class OrderRepository implements OrderRepositoryInterface
{
    private const ADDITION_DECORATORS = [
        'lettuce' => LettuceDecorator::class,
        'onion' => OnionDecorator::class,
        'pepper' => PepperDecorator::class,
        'cheese' => CheeseDecorator::class,
        'tomato' => TomatoDecorator::class,
    ];

    public function __construct(
        private PDO $connection,
        private ProductFactory $productFactory
    ) {}

    public function save(Order $order): void
    {
        $this->connection->beginTransaction();

        try {
            $stmt = $this->connection->prepare(
                <<<SQL
                    INSERT INTO orders (id, status, total_price, created_at, updated_at) 
                    VALUES (:id, :status, :total_price, :created_at, :updated_at)
                SQL
            );

            $stmt->execute([
                'id' => $order->getId(),
                'status' => $order->getStatus()->value,
                'total_price' => $order->getTotalPrice(),
                'created_at' => $order->getCreatedAt()->format('Y-m-d H:i:s'),
                'updated_at' => $order->getUpdatedAt()?->format('Y-m-d H:i:s'),
            ]);

            foreach ($order->getItems() as $item) {
                $this->saveOrderItem($order->getId(), $item);
            }

            foreach ($order->getStatusHistory() as $historyItem) {
                $this->addStatusHistory(
                    $order->getId(),
                    $historyItem['status'],
                    $historyItem['description']
                );
            }

            $this->connection->commit();
        } catch (Exception $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }

    private function saveOrderItem(string $orderId, ProductInterface $item): void
    {
        $stmt = $this->connection->prepare(
            <<<SQL
                INSERT INTO order_items (order_id, product_type, product_name, price, additions) 
                VALUES (:order_id, :product_type, :product_name, :price, :additions)
            SQL
        );

        $itemArray = $item->toArray();

        $stmt->execute([
            'order_id' => $orderId,
            'product_type' => $itemArray['type'] ?? 'unknown',
            'product_name' => $itemArray['name'] ?? 'Unknown Product',
            'price' => $item->getPrice(),
            'additions' => json_encode($itemArray['additions'] ?? []),
        ]);
    }

    public function findById(string $id): ?Order
    {
        $stmt = $this->connection->prepare(
            <<<SQL
                SELECT * FROM orders WHERE id = :id
            SQL
        );
        $stmt->execute(['id' => $id]);
        $orderData = $stmt->fetch();

        if (!$orderData) {
            return null;
        }

        $order = $this->hydrateOrder($orderData);

        return $order;
    }

    private function hydrateOrder(array $orderData): Order
    {
        $order = new Order($orderData['id']);

        $reflection = new ReflectionClass($order);

        $statusProperty = $reflection->getProperty('status');
        $statusProperty->setValue($order, OrderStatus::from($orderData['status']));

        $createdAtProperty = $reflection->getProperty('createdAt');
        $createdAtProperty->setValue($order, new DateTimeImmutable($orderData['created_at']));

        if ($orderData['updated_at']) {
            $updatedAtProperty = $reflection->getProperty('updatedAt');
            $updatedAtProperty->setValue($order, new DateTimeImmutable($orderData['updated_at']));
        }

        $items = $this->loadOrderItems($orderData['id']);
        $itemsProperty = $reflection->getProperty('items');
        $itemsProperty->setValue($order, $items);

        $statusHistory = $this->getStatusHistory($orderData['id']);
        $historyProperty = $reflection->getProperty('statusHistory');
        $historyProperty->setValue($order, $statusHistory);

        return $order;
    }

    private function loadOrderItems(string $orderId): array
    {
        $stmt = $this->connection->prepare(
            <<<SQL
                SELECT * FROM order_items WHERE order_id = :order_id
            SQL
        );
        $stmt->execute(['order_id' => $orderId]);
        $itemsData = $stmt->fetchAll();

        $items = [];
        foreach ($itemsData as $itemData) {
            try {
                $product = $this->productFactory->createProduct($itemData['product_type']);

                $additions = json_decode($itemData['additions'] ?? '[]', true) ?: [];
                $product = $this->applyAdditions($product, $additions);

                $items[] = $product;
            } catch (Exception $e) {
                continue;
            }
        }

        return $items;
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

    public function updateStatus(Order $order): void
    {
        $stmt = $this->connection->prepare(
            <<<SQL
                UPDATE orders SET status = :status, updated_at = :updated_at WHERE id = :id
            SQL
        );

        $stmt->execute([
            'id' => $order->getId(),
            'status' => $order->getStatus()->value,
            'updated_at' => $order->getUpdatedAt()?->format('Y-m-d H:i:s') ?? (new DateTimeImmutable())->format('Y-m-d H:i:s'),
        ]);

        $this->addStatusHistory(
            $order->getId(),
            $order->getStatus()->value,
            $order->getStatus()->getDescription()
        );
    }

    public function addStatusHistory(string $orderId, string $status, string $description): void
    {
        $stmt = $this->connection->prepare(
            <<<SQL
                INSERT INTO order_status_history (order_id, status, description, created_at) 
                VALUES (:order_id, :status, :description, :created_at)
            SQL
        );

        $stmt->execute([
            'order_id' => $orderId,
            'status' => $status,
            'description' => $description,
            'created_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
        ]);
    }

    public function getStatusHistory(string $orderId): array
    {
        $stmt = $this->connection->prepare(
            <<<SQL
                SELECT status, description, created_at FROM order_status_history 
                WHERE order_id = :order_id ORDER BY created_at ASC
            SQL
        );
        $stmt->execute(['order_id' => $orderId]);
        $history = $stmt->fetchAll();

        return array_map(fn($item) => [
            'status' => $item['status'],
            'timestamp' => $item['created_at'],
            'description' => $item['description'],
        ], $history);
    }

    public function findPendingOrders(): array
    {
        $stmt = $this->connection->prepare(
            <<<SQL
                SELECT * FROM orders 
                WHERE status NOT IN ('delivered', 'cancelled')
                ORDER BY created_at ASC
            SQL
        );
        $stmt->execute();
        $ordersData = $stmt->fetchAll();

        $orders = [];
        foreach ($ordersData as $orderData) {
            $orders[] = $this->hydrateOrder($orderData);
        }

        return $orders;
    }
}
