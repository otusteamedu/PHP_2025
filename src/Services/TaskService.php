<?php declare(strict_types=1);

namespace App\Tasks\Services;

use App\Tasks\Models\Task;

class TaskService
{
	private array $tasks = [];
	private string $storageFile;

	public function __construct()
	{
		$this->storageFile = __DIR__ . '/../../storage/tasks.json';
		$this->loadTasks();
	}

	public function createTask(array $data): Task
	{
		$task = new Task(['data' => $data]);
		$this->tasks[$task->id] = $task;
		$this->saveTasks();

		return $task;
	}

	public function getTask(string $id): ?Task
	{
		return $this->tasks[$id] ?? null;
	}

	/**
	 * @throws \Exception
	 */
	public function updateTask(string $id, array $updates): ?Task
	{
		if (!isset($this->tasks[$id])) {
			return null;
		}

		$task = $this->tasks[$id];

		foreach ($updates as $key => $value) {
			if (property_exists($task, $key)) {
				$task->$key = $value;
			}
		}

		$task->updated_at = date('c');
		$this->saveTasks();

		return $task;
	}

	private function loadTasks(): void
	{
		if (file_exists($this->storageFile)) {
			$data = json_decode(file_get_contents($this->storageFile), true) ?? [];
			foreach ($data as $taskData) {
				if (is_array($taskData)) {
					$task = new Task($taskData);
					$this->tasks[$task->id] = $task;
				}
			}
		}
	}

	private function saveTasks(): void
	{
		try {
			$data = array_map(fn($task) => $task->toArray(), $this->tasks);
			$json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

			if ($json === false) {
				throw new \RuntimeException('JSON encoding failed: ' . json_last_error_msg());
			}

			$result = file_put_contents($this->storageFile, $json, LOCK_EX);
			if ($result === false) {
				throw new \RuntimeException("Failed to write to storage file: {$this->storageFile}");
			}

			// Логируем успешное сохранение
			error_log("Successfully saved " . count($this->tasks) . " tasks to {$this->storageFile}");
		} catch (\Exception $e) {
			error_log("Error saving tasks: " . $e->getMessage());
			throw $e;
		}
	}
}