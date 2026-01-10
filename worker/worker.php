<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Infrastructure\RabbitConnection;
use App\Infrastructure\Database;
use App\Repository\TaskRepository;
use Dotenv\Dotenv;
use PhpAmqpLib\Message\AMQPMessage;

$root = dirname(__DIR__);

if (file_exists($root . '/.env')) {
    $dotenv = Dotenv::createImmutable($root);
    $dotenv->load();
    $dotenv->required([
        'DB_HOST', 'DB_PORT', 'DB_NAME', 'DB_USER', 'DB_PASS',
        'RABBITMQ_HOST', 'RABBITMQ_PORT', 'RABBITMQ_USER', 'RABBITMQ_PASS',
        'RABBITMQ_QUEUE', 'RABBITMQ_VHOST'
    ]);
}

$rabbit = new RabbitConnection(
    $_ENV['RABBITMQ_HOST'],
    (int) $_ENV['RABBITMQ_PORT'],
    $_ENV['RABBITMQ_USER'],
    $_ENV['RABBITMQ_PASS'],
    $_ENV['RABBITMQ_VHOST'],
    $_ENV['RABBITMQ_QUEUE']
);


$db = new Database($_ENV['DB_HOST'], (int) $_ENV['DB_PORT'], $_ENV['DB_NAME'],
    $_ENV['DB_USER'], $_ENV['DB_PASS']);

$repo = new TaskRepository($db);

[$connection, $channel] = $rabbit->channel();
$queue = $rabbit->getQueueName();

echo "Worker started. Waiting for messages on queue '{$queue}'..." . PHP_EOL;

$callback = function (AMQPMessage $msg) use ($repo) {
    $data = json_decode($msg->getBody(), true) ?: [];
    $id = $data['id'] ?? null;
    $payload = $data['payload'] ?? null;
    echo 'New message: ' . $id . ' ' . $payload . PHP_EOL;
    if (!$id) {
        // reject bad message
        $msg->ack();
        return;
    }

    // Симулируем обработку
    sleep(1);

    // Помечаем как обработано, фиксируем время
    $repo->markProcessed($id);

    $msg->ack();
};

$channel->basic_qos(null, 1, null);
$channel->basic_consume($queue, '', false, false, false, false, $callback);

while ($channel->is_consuming()) {
    $channel->wait();
}

$channel->close();
$connection->close();
