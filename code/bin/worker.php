<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Infrastructure\Database\DatabaseConnection;
use App\Infrastructure\Repositories\OrderRepository;
use App\Domain\Product\Factory\ProductFactory;
use App\Domain\Product\Strategy\BurgerStrategy;
use App\Domain\Product\Strategy\SandwichStrategy;
use App\Domain\Product\Strategy\HotDogStrategy;

use App\Domain\Enums\OrderStatus;

// Задержка между изменениями статуса
const STATUS_CHANGE_DELAY = 10;
// Интервал между итерациями проверки
const CHECK_INTERVAL = 5;
// Вероятность провала проверки качества
const QUALITY_CHECK_FAIL_CHANCE = 5;

echo "[Worker] Starting order processing worker...\n";

$maxRetries = 30;
$retryCount = 0;
$pdo = null;

while ($retryCount < $maxRetries) {
    try {
        $pdo = DatabaseConnection::getConnection();
        break;
    } catch (\Throwable $e) {
        $retryCount++;
        echo "[Worker] Waiting for PostgreSQL... ({$retryCount}/{$maxRetries})\n";
        sleep(2);
    }
}

if ($pdo === null) {
    echo "[Worker] Failed to connect to PostgreSQL after {$maxRetries} attempts\n";
    exit(1);
}

echo "[Worker] Connected to PostgreSQL\n";
echo "[Worker] Checking for pending orders every " . CHECK_INTERVAL . " seconds...\n";
echo "[Worker] Status change delay: " . STATUS_CHANGE_DELAY . " seconds\n";

$productFactory = new ProductFactory([
    new BurgerStrategy(),
    new SandwichStrategy(),
    new HotDogStrategy(),
]);
$orderRepository = new OrderRepository($pdo, $productFactory);

while (true) {
    try {
        $pendingOrders = $orderRepository->findPendingOrders();

        if (empty($pendingOrders)) {
            echo "[Worker] No pending orders, sleeping...\n";
            sleep(CHECK_INTERVAL);
            continue;
        }

        echo "[Worker] Found " . count($pendingOrders) . " pending order(s)\n";

        foreach ($pendingOrders as $order) {
            $orderId = $order->getId();
            $currentStatus = $order->getStatus();
            $nextStatus = $currentStatus->getNextStatus();

            if ($nextStatus === null) {
                echo "[Worker] Order {$orderId} is in final status: {$currentStatus->value}\n";
                continue;
            }

            // Проверка качества
            if ($currentStatus === OrderStatus::QUALITY_CHECK) {
                $failsQualityCheck = rand(1, 100) <= QUALITY_CHECK_FAIL_CHANCE;

                if ($failsQualityCheck) {
                    $orderRepository->addStatusHistory(
                        $orderId,
                        'quality_check_failed',
                        'Заказ не прошёл проверку качества, отправлен на повторное приготовление'
                    );

                    $order->setStatus(OrderStatus::PREPARING);
                    $orderRepository->updateStatus($order);

                    echo "[Worker] Order {$orderId}: FAILED quality check -> back to preparing\n";
                    continue;
                }
            }

            $order->setStatus($nextStatus);
            $orderRepository->updateStatus($order);

            echo "[Worker] Order {$orderId}: {$currentStatus->value} -> {$nextStatus->value}\n";

            if ($nextStatus === OrderStatus::DELIVERED) {
                echo "[Worker] Order {$orderId}: completed successfully!\n";
            }
        }

        sleep(STATUS_CHANGE_DELAY);
    } catch (\Throwable $e) {
        echo "[Worker] Error: " . $e->getMessage() . "\n";
        sleep(5);
    }
}
