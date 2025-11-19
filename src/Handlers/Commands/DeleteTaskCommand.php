<?php

namespace App\Handlers\Commands;

use App\Services\TaskService;
use App\Services\TelegramService;

class DeleteTaskCommand extends BaseCommand
{
    private TaskService $taskService;

    public function __construct(
        TelegramService $telegramService,
        TaskService $taskService
    ) {
        parent::__construct($telegramService);
        $this->taskService = $taskService;
    }

    public function execute(int $chatId, int $userId, string $args = ''): void
    {
        $taskId = (int)trim($args);

        if ($taskId <= 0) {
            $this->telegramService->sendMessage($chatId, "❌ Пожалуйста, укажите корректный ID задачи. Пример: /delete_task 1");
            return;
        }

        try {
            $task = $this->taskService->getTask($taskId, $userId);
            if (!$task) {
                $this->telegramService->sendMessage($chatId, "❌ Задача с ID {$taskId} не найдена.");
                return;
            }

            if ($this->taskService->deleteTask($taskId, $userId)) {
                $this->telegramService->sendMessage($chatId, "✅ Задача \"{$task->getTitle()}\" удалена.");
            } else {
                $this->telegramService->sendMessage($chatId, "❌ Ошибка при удалении задачи.");
            }
        } catch (\Exception $e) {
            error_log("Error deleting task: " . $e->getMessage());
            $this->telegramService->sendMessage($chatId, "❌ Ошибка базы данных при удалении задачи.");
        }
    }
}