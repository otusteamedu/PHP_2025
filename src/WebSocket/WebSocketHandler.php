<?php declare(strict_types=1);

namespace App\Bankhistory\WebSocket;

use SplObjectStorage;
use Ratchet\ConnectionInterface;
use PhpAmqpLib\Channel\AMQPChannel;
use Ratchet\MessageComponentInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class WebSocketHandler implements MessageComponentInterface
{
	protected SplObjectStorage      $clients;
	protected array                 $config;
	protected ?AMQPStreamConnection $rabbitConn        = NULL;
	protected ?AMQPChannel          $rabbitChannel     = NULL;
	protected bool                  $isRabbitConnected = false;
	protected array                 $consumers         = [];

	public function __construct()
	{
		$this->clients = new SplObjectStorage;
		$this->config = [
			'host' => $_ENV['RMQ_HOST'],
			'port' => $_ENV['RMQ_PORT'],
			'user' => $_ENV['RMQ_USER'],
			'password' => $_ENV['RMQ_PASSWORD'],
			'queue_name' => $_ENV['RMQ_QUEUE_NAME'],
		];

		$this->connectToRabbitMQ();

		echo "WebSocket server started\n";
	}

	private function connectToRabbitMQ(): void
	{
		$maxRetries = 3;
		$retryDelay = 2;

		for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
			try {
				echo "Attempting to connect to RabbitMQ (attempt $attempt/$maxRetries)...\n";

				$this->rabbitConn = new AMQPStreamConnection(
					$this->config['host'],
					$this->config['port'],
					$this->config['user'],
					$this->config['password']
				);

				$this->rabbitChannel = $this->rabbitConn->channel();
				$this->isRabbitConnected = true;

				echo "✅ Successfully connected to RabbitMQ\n";
				return;

			} catch (\Exception $e) {
				echo "❌ RabbitMQ connection failed (attempt $attempt/$maxRetries): " . $e->getMessage() . "\n";

				if ($attempt < $maxRetries) {
					echo "Retrying in {$retryDelay} seconds...\n";
					sleep($retryDelay);
					$retryDelay *= 2;
				} else {
					echo "❌ All connection attempts failed. Running in fallback mode.\n";
					$this->isRabbitConnected = false;
				}
			}
		}
	}

	public function onOpen(ConnectionInterface $conn): void
	{
		$this->clients->attach($conn);
		$conn->requestId = NULL;

		echo "✅ New WebSocket connection! ({$conn->resourceId})\n";

		$conn->send(json_encode([
			'type' => 'connection_established',
			'message' => 'Connected to WebSocket server',
			'rabbitmq_connected' => $this->isRabbitConnected,
			'timestamp' => date('Y-m-d H:i:s')
		]));
	}

	public function onMessage(ConnectionInterface $from, $msg): void
	{
		echo "📨 Received message from {$from->resourceId}: {$msg}\n";

		$data = json_decode($msg, true);

		if (!$data) {
			$this->sendError($from, 'Invalid JSON message');
			return;
		}

		if (!isset($data['type'])) {
			$this->sendError($from, 'Message type is required');
			return;
		}

		switch ($data['type']) {
			case 'subscribe':
				if (!isset($data['request_id'])) {
					$this->sendError($from, 'request_id is required for subscription');
					return;
				}
				$this->subscribeToUpdates($from, $data['request_id']);
				break;

			case 'ping':
				$from->send(json_encode(['type' => 'pong']));
				break;

			default:
				$this->sendError($from, 'Unknown message type: ' . $data['type']);
		}
	}

	public function onClose(ConnectionInterface $conn): void
	{
		echo "❌ Connection {$conn->resourceId} has disconnected\n";

		if (isset($conn->requestId)) {
			$this->unsubscribeFromQueue($conn->requestId);
			$this->cleanupQueue($conn->requestId);
		}

		$this->clients->detach($conn);
	}

	public function onError(ConnectionInterface $conn, \Exception $e): void
	{
		echo "💥 WebSocket error for connection {$conn->resourceId}: {$e->getMessage()}\n";

		$conn->send(json_encode([
			'type' => 'error',
			'message' => 'WebSocket error: ' . $e->getMessage()
		]));

		$conn->close();
	}

	private function subscribeToUpdates(ConnectionInterface $conn, $requestId): void
	{
		try {
			if (!$this->isRabbitConnected) {
				$this->sendError($conn, 'RabbitMQ is not available. Cannot subscribe to updates.');
				return;
			}

			$conn->requestId = $requestId;
			$callbackQueue = 'callback_' . $requestId;

			// Создаем очередь для callback сообщений
			$this->rabbitChannel->queue_declare(
				$callbackQueue,
				false,
				false,
				false,
				true // auto-delete
			);

			// Начинаем потреблять сообщения из очереди
			$this->startConsumingQueue($conn, $callbackQueue);

			// Отправляем подтверждение подписки
			$conn->send(json_encode([
				'type' => 'subscribed',
				'request_id' => $requestId,
				'message' => 'Successfully subscribed to updates'
			]));

			echo "✅ Client {$conn->resourceId} subscribed to request: {$requestId}\n";

		} catch (\Exception $e) {
			$this->sendError($conn, 'Failed to subscribe: ' . $e->getMessage());
		}
	}

	private function startConsumingQueue(ConnectionInterface $conn, $queueName): void
	{
		if (!$this->isRabbitConnected) {
			return;
		}

		$callback = function ($msg) use ($conn) {
			try {
				echo "📤 Forwarding message to client {$conn->resourceId}: {$msg->body}\n";

				// Проверяем, что соединение еще активно
				if ($this->clients->contains($conn)) {
					$conn->send($msg->body);
					$msg->ack();
					echo "✅ Message acknowledged and sent to client\n";

					// Если это завершающее сообщение, отписываемся от очереди
					$data = json_decode($msg->body, true);
					if (isset($data['type']) && $data['type'] === 'completed') {
						$this->unsubscribeFromQueue($conn->requestId);
						echo "✅ Unsubscribed from queue after completion\n";
					}
				} else {
					echo "⚠️  Client disconnected, message not delivered\n";
					$msg->nack(); // Не подтверждаем сообщение если клиент отключился
				}
			} catch (\Exception $e) {
				echo "💥 Error forwarding message: " . $e->getMessage() . "\n";
				$msg->nack(); // Не подтверждаем сообщение при ошибке
			}
		};

		$consumerTag = $this->rabbitChannel->basic_consume(
			$queueName,
			'',
			false,
			false,
			false,
			false,
			$callback
		);

		// Сохраняем информацию о consumer для последующей отмены
		$this->consumers[$conn->requestId] = $consumerTag;
		$conn->consumerTag = $consumerTag;

		echo "✅ Started consuming from queue: {$queueName} with consumer tag: {$consumerTag}\n";
	}

	private function unsubscribeFromQueue($requestId): void
	{
		if (!$this->isRabbitConnected || !isset($this->consumers[$requestId])) {
			return;
		}

		try {
			$consumerTag = $this->consumers[$requestId];
			$this->rabbitChannel->basic_cancel($consumerTag);
			unset($this->consumers[$requestId]);
			echo "✅ Unsubscribed from queue for request: {$requestId}\n";
		} catch (\Exception $e) {
			echo "💥 Error unsubscribing from queue: " . $e->getMessage() . "\n";
		}
	}

	private function cleanupQueue($requestId): void
	{
		if (!$this->isRabbitConnected) {
			return;
		}

		try {
			$callbackQueue = 'callback_' . $requestId;
			$this->rabbitChannel->queue_delete($callbackQueue);
			echo "✅ Queue {$callbackQueue} cleaned up\n";
		} catch (\Exception $e) {
			echo "💥 Error cleaning up queue: " . $e->getMessage() . "\n";
		}
	}

	private function sendError(ConnectionInterface $conn, $message): void
	{
		$errorData = [
			'type' => 'error',
			'message' => $message,
			'timestamp' => date('Y-m-d H:i:s')
		];

		$conn->send(json_encode($errorData));
		echo "❌ Sent error to client {$conn->resourceId}: {$message}\n";
	}

	// Метод для обработки RabbitMQ сообщений (вызывается периодически)
	public function processPendingMessages(): void
	{
		if (!$this->isRabbitConnected || !$this->rabbitChannel) {
			return;
		}

		try {
			// Обрабатываем все pending сообщения без блокировки
			while ($this->rabbitChannel->is_consuming()) {
				$this->rabbitChannel->wait(NULL, true);
			}
		} catch (\Exception $e) {
			echo "💥 Error processing pending messages: " . $e->getMessage() . "\n";
		}
	}

	public function __destruct()
	{
		if ($this->isRabbitConnected) {
			try {
				if ($this->rabbitChannel) {
					$this->rabbitChannel->close();
				}
				if ($this->rabbitConn) {
					$this->rabbitConn->close();
				}
				echo "✅ RabbitMQ connection closed\n";
			} catch (\Exception $e) {
				echo "💥 Error closing RabbitMQ connection: " . $e->getMessage() . "\n";
			}
		}
	}
}