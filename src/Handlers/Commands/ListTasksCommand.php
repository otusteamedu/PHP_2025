<?php

namespace App\Handlers\Commands;

use App\Services\TelegramService;
use App\Services\TaskService;
use App\Models\Task;

class ListTasksCommand extends BaseCommand
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
        try {
            $tasks = $this->taskService->getUserTasks($userId);
    
            if (empty($tasks)) {
                $this->telegramService->sendMessage($chatId, "📝 У вас пока нет задач. Создайте первую с помощью /new_task");
                return;
            }
    
            $message = "📋 Ваши задачи:\n\n";
            
            foreach ($tasks as $task) {
                $statusEmoji = $task->getStatus() === Task::STATUS_COMPLETED ? '✅' : '⏳';
                $priorityEmoji = $this->getPriorityEmoji($task->getPriority());
                
                $message .= "{$statusEmoji} #{$task->getId()} - {$task->getTitle()} {$priorityEmoji}\n";
                
                if ($task->getDueDate()) {
                    $message .= "   📅 Срок: {$task->getDueDate()}\n";
                }
    
                // Показываем информацию о напоминании
                if ($task->hasReminder()) {
                    $reminderType = $this->getReminderTypeText($task->getReminderType());
                    $message .= "   🔔 Напоминание: {$reminderType}\n";
                    
                    if ($task->getReminderTime()) {
                        $message .= "   ⏰ Время: {$task->getReminderTime()}\n";
                    }
                }
                
                // Показываем команды для управления статусом
                if ($task->getStatus() === Task::STATUS_COMPLETED) {
                    $message .= "   🔄 Вернуть в работу: /incomplete_task {$task->getId()}\n";
                } else {
                    $message .= "   ✅ Выполнить: /complete_task {$task->getId()}\n";
                }
                
                $message .= "\n";
            }
    
            $this->telegramService->sendMessage($chatId, $message);
        } catch (\Exception $e) {
            error_log("Error listing tasks: " . $e->getMessage());
            $this->telegramService->sendMessage($chatId, "❌ Ошибка при получении списка задач.");
        }
    }
}