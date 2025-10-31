<?php

require_once __DIR__ . '/../src/init.php';

use PhpAmqpLib\Message\AMQPMessage;
use App\Bankhistory\Worker\StatementWorker;
use PhpAmqpLib\Connection\AMQPStreamConnection;

$config = [
	'host' => $_ENV['RMQ_HOST'],
	'port' => $_ENV['RMQ_PORT'],
	'user' => $_ENV['RMQ_USER'],
	'password' => $_ENV['RMQ_PASSWORD'],
	'queue_name' => $_ENV['RMQ_QUEUE_NAME'],
];

try {
	$connection = new AMQPStreamConnection(
		$config['host'],
		$config['port'],
		$config['user'],
		$config['password']
	);
} catch (Exception $e) {
	die($e->getMessage());
}

$channel = $connection->channel();
$channel->queue_declare($config['queue_name'], false, true, false, false);

$worker = new StatementWorker();

echo " [*] Ожидание сообщений. Для выхода нажмите CTRL+C\n";

$callback = function ($msg) {
	global $worker;

	echo " [x] Получено сообщение: " . $msg->body . "\n";

	$data = json_decode($msg->body, true);

	try {
		$worker->processBankStatementRequest($data);
		echo " [x] Запрос обработан успешно\n";
		$msg->ack();
	} catch (Exception $e) {
		echo " [x] Ошибка обработки: " . $e->getMessage() . "\n";

		// Отправка ошибки через WebSocket
		$worker->sendProgressUpdate(
			$data['request_id'],
			'error',
			0,
			'Ошибка обработки: ' . $e->getMessage()
		);
	}
};

$channel->basic_qos(NULL, 1, NULL);
$channel->basic_consume($config['queue_name'], '', false, false, false, false, $callback);

while ($channel->is_open()) {
	$channel->wait();
}

$channel->close();
$connection->close();