<?php declare(strict_types=1);

namespace App\Tasks\Workers;

use App\Tasks\Services\RabbitMQService;
use App\Tasks\Services\TaskService;
use App\Tasks\Models\Task;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class TaskWorker
{
	private RabbitMQService $rabbitMQ;
	private TaskService $taskService;
	private Logger $logger;
	private bool $shouldStop = false;

	public function __construct()
	{
		$this->rabbitMQ = new RabbitMQService();
		$this->taskService = new TaskService();

		$this->logger = new Logger('worker');
		$this->logger->pushHandler(new StreamHandler('php://stdout'));

		// Обработка сигналов для graceful shutdown
		pcntl_async_signals(true);
		pcntl_signal(SIGTERM, [$this, 'handleSignal']);
		pcntl_signal(SIGINT, [$this, 'handleSignal']);
	}

	public function handleSignal(int $signal): void
	{
		$this->logger->info("Received signal: {$signal}, shutting down gracefully...");
		$this->shouldStop = true;
	}

	public function start(): void
	{
		$this->logger->info('Worker started and waiting for messages...');

		$callback = function ($msg) {
			try {
				$data = json_decode($msg->body, true);
				$taskId = $data['task_id'] ?? null;

				if (!$taskId) {
					$this->logger->error('No task_id in message');
					$msg->ack();
					return;
				}

				$this->logger->info("Processing task: {$taskId}");

				// Проверяем существование задачи
//				$task = $this->taskService->getTask($taskId);
//				if (!$task) {
//					$this->logger->error("Task not found: {$taskId}");
//					$msg->ack();
//					return;
//				}

				// Обновляем статус на "обрабатывается"
				$this->taskService->updateTask($taskId, [
					'status' => Task::STATUS_PROCESSING
				]);

				$this->logger->info("Started processing task: {$taskId}");

				// Эмуляция обработки задачи
				$this->processTask($taskId, $data['data'] ?? []);

				// Подтверждаем обработку сообщения
				$msg->ack();

				$this->logger->info("Task completed: {$taskId}");

			} catch (\Exception $e) {
				$this->logger->error("Error processing task: " . $e->getMessage());

				// Обновляем статус на "ошибка"
				if (isset($taskId)) {
					$this->taskService->updateTask($taskId, [
						'status' => Task::STATUS_FAILED,
						'result' => ['error' => $e->getMessage()]
					]);
				}

				// Подтверждаем сообщение даже при ошибке, чтобы не зацикливать
				if (isset($msg)) {
					$msg->ack();
				}
			}
		};

		try {
			$this->rabbitMQ->consume($callback);
		} catch (\Exception $e) {
			$this->logger->error("Worker error: " . $e->getMessage());
			sleep(5); // Ждем перед перезапуском
		}
	}

	private function processTask(string $taskId, array $data): void
	{
		// Эмуляция длительной обработки (10-15 секунд для теста)
		$processingTime = rand(10, 15);
		$this->logger->info("Task {$taskId}: Processing for {$processingTime} seconds");

		for ($i = 1; $i <= $processingTime; $i++) {
			if ($this->shouldStop) {
				$this->logger->info("Task {$taskId}: Processing interrupted");
				return;
			}
			sleep(1);
			$this->logger->info("Task {$taskId}: Processing... {$i}/{$processingTime}");
			$this->taskService->updateTask($taskId, [
				'status' => Task::STATUS_PROCESSING,
				'updated_at' => date('c'),
			]);
		}

		// Эмуляция результата обработки
		$result = [
			'processed_at' => date('c'),
			'processing_time' => $processingTime,
			'message' => 'Task processed successfully',
			'input_data' => $data,
			'output' => [
				'result' => 'success',
				'data_size' => count($data),
				'timestamp' => time()
			]
		];

		// Обновляем статус на "завершено"
		$this->taskService->updateTask($taskId, [
			'status' => Task::STATUS_COMPLETED,
			'result' => $result
		]);

		$this->logger->info("Task {$taskId}: Completed successfully");
	}

}