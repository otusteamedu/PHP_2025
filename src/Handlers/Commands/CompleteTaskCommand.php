<?php

namespace App\Handlers\Commands;

use App\Services\TaskService;
use App\Models\Task;
use App\Services\TelegramService;

class CompleteTaskCommand extends BaseCommand
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
            $this->telegramService->sendMessage($chatId, 
                "❌ Пожалуйста, укажите корректный ID задачи. Пример: /complete_task 1"
            );
            return;
        }

        try {
            $task = $this->taskService->getTask($taskId, $userId);
            if (!$task) {
                $this->telegramService->sendMessage($chatId, "❌ Задача с ID {$taskId} не найдена.");
                return;
            }

            if ($task->getStatus() === Task::STATUS_COMPLETED) {
                $this->telegramService->sendMessage($chatId, "ℹ️ Задача \"{$task->getTitle()}\" уже выполнена.");
                return;
            }

            // Обновляем статус задачи
            $updatedTask = $this->taskService->updateTask($taskId, $userId, ['status' => Task::STATUS_COMPLETED]);

            if ($updatedTask) {
                $message = "✅ Задача выполнена!\n\n";
                $message .= "📌 Задача: {$updatedTask->getTitle()}\n";
                $message .= "🆔 ID: {$updatedTask->getId()}\n";
                $message .= "🎯 Статус: Выполнено";
                
                $this->telegramService->sendMessage($chatId, $message);
            } else {
                $this->telegramService->sendMessage($chatId, "❌ Ошибка при выполнении задачи.");
            }
        } catch (\Exception $e) {
            error_log("Error completing task: " . $e->getMessage());
            $this->telegramService->sendMessage($chatId, "❌ Ошибка базы данных при выполнении задачи.");
        }
    }
}