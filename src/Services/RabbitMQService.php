<?php declare(strict_types=1);

namespace App\Tasks\Services;

use PhpAmqpLib\Exception\AMQPTimeoutException;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class RabbitMQService
{
	private AMQPStreamConnection $connection;
	private \PhpAmqpLib\Channel\AMQPChannel $channel;
	private Logger $logger;
	private array $config;

	public function __construct()
	{
		$this->logger = new Logger('services_rabbitmq');
		$this->logger->pushHandler(new StreamHandler('php://stdout'));

		$this->config = [
			'host' => $_ENV['RMQ_HOST'],
			'port' => $_ENV['RMQ_PORT'],
			'user' => $_ENV['RMQ_USER'],
			'pass' => $_ENV['RMQ_PASSWORD'],
			'queue_name' => $_ENV['RMQ_QUEUE_NAME'],
		];

		try {
			$this->connection = new AMQPStreamConnection(
				$this->config['host'],
				$this->config['port'],
				$this->config['user'],
				$this->config['pass']
			);
			$this->channel = $this->connection->channel();
			$this->channel->queue_declare($this->config['queue_name'], false, true, false, false);
			$this->logger->info('RabbitMQ connected successfully');
		} catch (\Exception $e) {
			$this->logger->error('RabbitMQ connection failed: ' . $e->getMessage());
			throw new \RuntimeException('Cannot connect to RabbitMQ: ' . $e->getMessage());
		}
	}

	/**
	 * @throws \Exception
	 */
	public function publishMessage(array $message): void
	{
		if (!$this->channel || !$this->connection) {
			throw new \RuntimeException('RabbitMQ not connected');
		}

		try {
			$msg = new AMQPMessage(
				json_encode($message),
				['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]
			);

			$this->channel->basic_publish($msg, '', $this->config['queue_name']);
			$this->logger->info('Message published to queue: ' . json_encode($message));
		} catch (\Exception $e) {
			$this->logger->error('Failed to publish message: ' . $e->getMessage());
			throw $e;
		}
	}

	public function consume(callable $callback): void
	{
		if (!$this->channel) {
			throw new \RuntimeException('RabbitMQ channel not available');
		}

		$this->channel->basic_qos(null, 1, null);
		$this->channel->basic_consume(
			$this->config['queue_name'],
			'',
			false,
			false,
			false,
			false,
			$callback
		);

		$this->logger->info('Waiting for messages...');

		try {
			while ($this->channel->is_consuming()) {
				$this->channel->wait();
			}
		} catch (AMQPTimeoutException $e) {
			$this->logger->info('Consumer timeout');
		} catch (\Exception $e) {
			$this->logger->error('Consumer error: ' . $e->getMessage());
			throw $e;
		}
	}

	public function __destruct()
	{
		try {
			if ($this->channel && $this->channel->is_open()) {
				$this->channel->close();
			}
			if ($this->connection) {
				$this->connection->close();
			}
		} catch (\Exception $e) {
			// Игнорируем ошибки при деструкторе
		}
	}
}