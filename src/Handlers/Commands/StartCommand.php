<?php

namespace App\Handlers\Commands;

class StartCommand extends BaseCommand
{
    public function execute(int $chatId, int $userId, string $args = ''): void
    {
        $message = "👋 Добро пожаловать в Task Manager Bot!\n\n";
        $message .= "📝 Доступные команды:\n";
        $message .= "/new_task - Создать новую задачу\n";
        $message .= "/list_tasks - Показать все задачи\n";
        $message .= "/complete_task <ID> - Выполнить задачу\n";
        $message .= "/incomplete_task <ID> - Вернуть в работу\n";
        $message .= "/delete_task <ID> - Удалить задачу\n";
        $message .= "/set_reminder <ID> - Настроить напоминание\n";
        $message .= "/remove_reminder <ID> - Удалить напоминание\n";
        $message .= "/stats - Показать статистику\n";
        $message .= "/cancel - Отменить текущее действие";
        
        $this->telegramService->sendMessage($chatId, $message);
    }
}