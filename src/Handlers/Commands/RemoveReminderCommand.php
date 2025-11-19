<?php

namespace App\Handlers\Commands;

use App\Services\TaskService;
use App\Services\ReminderService;
use App\Services\TelegramService;

class RemoveReminderCommand extends BaseCommand
{
    private TaskService $taskService;
    private ReminderService $reminderService;

    public function __construct(
        TelegramService $telegramService,
        TaskService $taskService,
        ReminderService $reminderService
    ) {
        parent::__construct($telegramService);
        $this->taskService = $taskService;
        $this->reminderService = $reminderService;
    }

    public function execute(int $chatId, int $userId, string $args = ''): void
    {
        $taskId = (int)trim($args);

        if ($taskId <= 0) {
            $this->telegramService->sendMessage($chatId, 
                "❌ Пожалуйста, укажите корректный ID задачи. Пример: /remove_reminder 1"
            );
            return;
        }

        $task = $this->taskService->getTask($taskId, $userId);
        if (!$task) {
            $this->telegramService->sendMessage($chatId, "❌ Задача с ID {$taskId} не найдена.");
            return;
        }

        if (!$task->hasReminder()) {
            $this->telegramService->sendMessage($chatId, "ℹ️ У этой задачи нет напоминания.");
            return;
        }

        $success = $this->reminderService->removeReminder($task);
        $message = $success ? "✅ Напоминание удалено." : "❌ Ошибка при удалении напоминания.";
        $this->telegramService->sendMessage($chatId, $message);
    }
}