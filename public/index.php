<?php
require_once __DIR__ . '/../src/init.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$config = [
	'host' => $_ENV['RMQ_HOST'],
	'port' => $_ENV['RMQ_PORT'],
	'user' => $_ENV['RMQ_USER'],
	'password' => $_ENV['RMQ_PASSWORD'],
	'queue_name' => $_ENV['RMQ_QUEUE_NAME'],
];

// Обработка AJAX запроса
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax'])) {
	header('Content-Type: application/json');

	$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
	$startDate = DateTime::createFromFormat('Y-m-d', $_POST['start_date']);
	$endDate = DateTime::createFromFormat('Y-m-d', $_POST['end_date']);

	if (!$email || !$startDate || !$endDate || $startDate > $endDate) {
		echo json_encode(['success' => false, 'error' => 'Пожалуйста, проверьте введенные данные']);
		exit;
	}

	try {
		$connection = new AMQPStreamConnection(
			$config['host'],
			$config['port'],
			$config['user'],
			$config['password']
		);

		$channel = $connection->channel();
		$channel->queue_declare($config['queue_name'], false, true, false, false);

		$requestData = [
			'email' => $email,
			'start_date' => $_POST['start_date'],
			'end_date' => $_POST['end_date'],
			'request_id' => uniqid('req_', true),
			'created_at' => date('Y-m-d H:i:s')
		];

		$message = new AMQPMessage(
			json_encode($requestData),
			['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]
		);

		$channel->basic_publish($message, '', $config['queue_name']);

		$channel->close();
		$connection->close();

		echo json_encode([
			'success' => true,
			'message' => 'Запрос принят в обработку',
			'request_id' => $requestData['request_id']
		]);

	} catch (Exception $e) {
		error_log("Error submitting request: " . $e->getMessage());
		echo json_encode(['success' => false, 'error' => 'Ошибка при обработке запроса: ' . $e->getMessage()]);
	}
	exit;
}

// Отображение формы
include __DIR__ . '/templates/form.php';