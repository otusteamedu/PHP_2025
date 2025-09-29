<?php declare(strict_types=1);

require_once __DIR__ . '/../src/init.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use App\Bankhistory\WebSocket\WebSocketHandler;

$config = [
	'host' => $_ENV['RMQ_HOST'],
	'port' => $_ENV['RMQ_PORT'],
	'user' => $_ENV['RMQ_USER'],
	'password' => $_ENV['RMQ_PASSWORD'],
	'queue_name' => $_ENV['RMQ_QUEUE_NAME'],
];

// Создаем WebSocket handler
$webSocketHandler = new WebSocketHandler();

$server = IoServer::factory(
	new HttpServer(
		new WsServer($webSocketHandler)
	),
	8080
);

echo "🚀 Starting WebSocket server on 0.0.0.0:8080\n";

// Добавляем периодическую обработку RabbitMQ сообщений
$server->loop->addPeriodicTimer(0.1, function () use ($webSocketHandler) {
	$webSocketHandler->processPendingMessages();
});

$server->run();