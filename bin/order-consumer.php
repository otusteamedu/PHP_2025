<?php
declare(strict_types=1);

use App\Repositories\OrderRepository;
use App\Services\RabbitMQService;
use DI\ContainerBuilder;

require __DIR__ . '/../vendor/autoload.php';

$envPath = dirname(__DIR__);
if (file_exists($envPath . '/.env')) {
    Dotenv\Dotenv::createImmutable($envPath)->load();
}

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(require __DIR__ . '/../src/config/container.php');
$container = $containerBuilder->build();

/** @var RabbitMQService $mq */
$mq = $container->get(RabbitMQService::class);
/** @var OrderRepository $orders */
$orders = $container->get(OrderRepository::class);

echo "[INFO] Order consumer started. Waiting for messages..." . PHP_EOL;

$mq->consume(RabbitMQService::ORDERS_QUEUE, function (array $msg) use ($mq, $orders) {
    $orderId = (int)($msg['order_id'] ?? 0);
    $email = (string)($msg['email'] ?? '');
    $dateFrom = (string)($msg['date_from'] ?? '');
    $dateTo = (string)($msg['date_to'] ?? '');

    echo sprintf("[ORDER] id=%d email=%s period=%s..%s\n", $orderId, $email, $dateFrom, $dateTo);
    // Имитация долгой генерации отчёта
    sleep(2);

    // Отметить заказ как сгенерированный
    if ($orderId > 0) {
        $orders->markGenerated($orderId);
    }

    // Отправить задачу на уведомление
    $mq->publish(RabbitMQService::NOTIFICATIONS_QUEUE, [
        'order_id' => $orderId,
        'email' => $email,
        'status' => 'ready',
        'sent_at' => time(),
    ]);

    echo "[ORDER] processed and notification queued\n";
});
