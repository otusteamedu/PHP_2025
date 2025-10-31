<?php declare(strict_types=1);

namespace App\Bankhistory\Worker;

use PhpAmqpLib\Message\AMQPMessage;
use App\Bankhistory\Sendler\EmailSendler;
use App\Bankhistory\Sendler\TelegramSendler;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class StatementWorker
{
	protected $config;

	public function __construct()
	{
		$this->config = [
			'host' => $_ENV['RMQ_HOST'],
			'port' => $_ENV['RMQ_PORT'],
			'user' => $_ENV['RMQ_USER'],
			'password' => $_ENV['RMQ_PASSWORD']
		];
	}

	public function sendProgressUpdate($requestId, $step, $progress, $message = ''): void
	{
		try {
			$connection = new AMQPStreamConnection(
				$this->config['host'],
				$this->config['port'],
				$this->config['user'],
				$this->config['password']
			);

			$channel = $connection->channel();
			$callbackQueue = 'callback_' . $requestId;

			$progressData = [
				'type' => 'progress',
				'request_id' => $requestId,
				'step' => $step,
				'progress' => $progress,
				'message' => $message,
				'timestamp' => date('Y-m-d H:i:s')
			];

			$message = new AMQPMessage(json_encode($progressData));
			$channel->basic_publish($message, '', $callbackQueue);

			$channel->close();
			$connection->close();

			echo " [x] Progress update sent: {$step} ({$progress}%)\n";

		} catch (\Exception $e) {
			echo " [x] Error sending progress: " . $e->getMessage() . "\n";
		}
	}

	public function processBankStatementRequest($data): array
	{
		echo " [x] Обработка запроса ID: " . $data['request_id'] . "\n";
		echo " [x] Период: " . $data['start_date'] . " - " . $data['end_date'] . "\n";
		echo " [x] Email: " . $data['email'] . "\n";

		// Этапы обработки с прогрессом
		$steps = [
			['step' => 'started', 'progress' => 0, 'message' => 'Запрос принят в обработку'],
			['step' => 'validating', 'progress' => 20, 'message' => 'Проверка данных'],
			['step' => 'processing', 'progress' => 40, 'message' => 'Обработка транзакций'],
			['step' => 'generating', 'progress' => 60, 'message' => 'Генерация выписки'],
			['step' => 'finalizing', 'progress' => 80, 'message' => 'Формирование отчета'],
			['step' => 'completed', 'progress' => 100, 'message' => 'Обработка завершена']
		];

		foreach ($steps as $stepInfo) {
			sleep(rand(2, 4)); // Имитация обработки

			$this->sendProgressUpdate(
				$data['request_id'],
				$stepInfo['step'],
				$stepInfo['progress'],
				$stepInfo['message']
			);

			echo " [x] Completed step: {$stepInfo['step']}\n";
		}

		// Генерация фиктивной выписки
		$statement = $this->generateStatement($data);

		// Отправка результата
		$this->sendCompletion($data['request_id'], $statement);

		// Отправка email
		$this->sendEmailNotification($data['email'], $statement);

		// Отправка Telegram
		if ($_ENV['TG_BOT_TOKEN'])
			$this->sendTelegramNotification($statement);

		return $statement;
	}

	protected function generateStatement($data): array
	{
		$statement = [
			'request_id' => $data['request_id'],
			'period' => $data['start_date'] . ' - ' . $data['end_date'],
			'generated_at' => date('Y-m-d H:i:s'),
			'transactions' => [],
			'total_income' => rand(1000, 50000),
			'total_expense' => rand(500, 25000),
			'balance' => rand(50000, 200000),
			'currency' => 'RUB'
		];

		// Генерация фиктивных транзакций
		$transactionTypes = ['Пополнение', 'Платеж', 'Перевод', 'Снятие наличных'];
		for ($i = 0; $i < rand(5, 15); $i++) {
			$statement['transactions'][] = [
				'id' => $i + 1,
				'date' => date('Y-m-d', strtotime($data['start_date'] . ' + ' . rand(0, 30) . ' days')),
				'description' => 'Транзакция ' . ($i + 1),
				'type' => $transactionTypes[array_rand($transactionTypes)],
				'amount' => rand(100, 10000),
				'currency' => 'RUB'
			];
		}

		return $statement;
	}

	protected function sendCompletion($requestId, $statement): void
	{
		try {
			$connection = new AMQPStreamConnection(
				$this->config['host'],
				$this->config['port'],
				$this->config['user'],
				$this->config['password']
			);

			$channel = $connection->channel();
			$callbackQueue = 'callback_' . $requestId;

			$completionData = [
				'type' => 'completed',
				'request_id' => $requestId,
				'data' => $statement,
				'timestamp' => date('Y-m-d H:i:s')
			];

			$msg = new AMQPMessage(json_encode($completionData));
			$channel->basic_publish($msg, '', $callbackQueue);

			$channel->close();
			$connection->close();

			echo " [x] Completion sent for request: {$requestId}\n";

		} catch (\Exception $e) {
			echo " [x] Error sending completion: " . $e->getMessage() . "\n";
		}
	}

	protected function sendEmailNotification($email, $statement): true
	{
		echo " [x] Отправка email на: " . $email . "\n";

		$sendler = new EmailSendler();

		$message = "<b>Ваша выписка сгенерирована.</b> <br>";
		$message .= "Период: " . $statement['period'] . "<br>";
		$message .= "Общий доход: " . $statement['total_income'] . " RUB <br>";
		$message .= "Общий расход: " . $statement['total_expense'] . " RUB <br>";
		$message .= "Баланс: " . $statement['balance'] . " RUB <br>";
		$message .= "Количество транзакций: " . count($statement['transactions']) . "<br>";
		$message .= "Сгенерировано: " . $statement['generated_at'] . "<br>";

		$message .= '<table style="border: 1px solid black; border-collapse: collapse; margin-top: 5px">';
		$message .= '<thead><tr>';
		$message .= '<th style="border: 1px solid black; border-collapse: collapse; padding: 3px">Дата</th>';
		$message .= '<th style="border: 1px solid black; border-collapse: collapse; padding: 3px">Описание</th>';
		$message .= '<th style="border: 1px solid black; border-collapse: collapse; padding: 3px">Тип операции</th>';
		$message .= '<th style="border: 1px solid black; border-collapse: collapse; padding: 3px">Сумма</th>';
		$message .= '</tr></thead>';
		$message .= '<tbody>';

		foreach ($statement['transactions'] as $transaction) {
			$symbol = $transaction['type'] === 'Пополнение' ? '+ ' : '- ';
			$amount = $transaction['amount'] ?? 0;
			$message .= '<tr>';
			$message .= '<td style="border: 1px solid black; border-collapse: collapse; padding: 3px">';
			$message .= $transaction['date'];
			$message .= '</td>';
			$message .= '<td style="border: 1px solid black; border-collapse: collapse; padding: 3px">';
			$message .= $transaction['description'];
			$message .= '</td>';
			$message .= '<td style="border: 1px solid black; border-collapse: collapse; padding: 3px">';
			$message .= $transaction['type'] ?? 'Неизвестно';
			$message .= '</td>';
			$message .= '<td style="border: 1px solid black; border-collapse: collapse; padding: 3px">';
			$message .= $symbol . $amount . ' RUB';
			$message .= '</td>';
			$message .= '</tr>';
		}
		$message .= '</tbody>';
		$message .= '</table>';


		$res = $sendler->send(
			$email,
			$_ENV['EMAIL_FROM'],
			"Банковская выписка за период " . $statement['period'],
			$message
		);

		return $res;
	}

	protected function sendTelegramNotification($statement): bool
	{
		echo " [x] Отправка выписки в Telegram \n";

		$sendler = new TelegramSendler();

		$chatId = $sendler->getUpdates();

		$message = "<b>Ваша выписка сгенерирована.</b> \n";
		$message .= "Период: " . $statement['period'] . "\n";
		$message .= "Общий доход: " . $statement['total_income'] . " RUB \n";
		$message .= "Общий расход: " . $statement['total_expense'] . " RUB \n";
		$message .= "Баланс: " . $statement['balance'] . " RUB \n";
		$message .= "Количество транзакций: " . count($statement['transactions']) . "\n";
		$message .= "Сгенерировано: " . $statement['generated_at'] . "\n";

		foreach ($statement['transactions'] as $transaction) {
			$symbol = $transaction['type'] === 'Пополнение' ? '+ ' : '- ';
			$amount = $transaction['amount'] ?? 0;
			$dataTable[] = [
				$transaction['date'],
				$transaction['description'],
				$transaction['type'] ?? 'Неизвестно',
				$symbol . $amount . ' RUB',
			];
		}
		$message .= $sendler->createTable(
			['Дата', 'Описание', 'Тип операции', 'Сумма'],
			$dataTable
		);

		try {
			$sendler->sendMessage($chatId, $message);
			return true;
		} catch (\Exception $e) {
			return false;
		}

	}

}