<?php

namespace App\Handlers\Commands;

use App\Services\TaskService;
use App\Repositories\UserStateRepository;
use App\Models\UserState;
use App\Models\Task;
use App\Services\TelegramService;

class SetReminderCommand extends BaseCommand
{
    private TaskService $taskService;
    private UserStateRepository $userStateRepository;

    public function __construct(
        TelegramService $telegramService,
        TaskService $taskService,
        UserStateRepository $userStateRepository
    ) {
        parent::__construct($telegramService);
        $this->taskService = $taskService;
        $this->userStateRepository = $userStateRepository;
    }

    public function execute(int $chatId, int $userId, string $args = ''): void
    {
        $taskId = (int)trim($args);

        if ($taskId <= 0) {
            $this->telegramService->sendMessage($chatId, 
                "❌ Пожалуйста, укажите корректный ID задачи. Пример: /set_reminder 1"
            );
            return;
        }

        $task = $this->taskService->getTask($taskId, $userId);
        if (!$task) {
            $this->telegramService->sendMessage($chatId, "❌ Задача с ID {$taskId} не найдена.");
            return;
        }

        // Сохраняем состояние для настройки напоминания
        $userState = new UserState($userId, UserState::STATE_AWAITING_REMINDER_TYPE, ['task_id' => $taskId]);
        $this->userStateRepository->save($userState);

        $this->showReminderTypeKeyboard($chatId, $task);
    }

    private function showReminderTypeKeyboard(int $chatId, Task $task): void
    {
        $buttons = [
            [
                ['text' => '⏰ Однократное', 'callback_data' => 'reminder_type_one_time'],
                ['text' => '📅 Ежедневное', 'callback_data' => 'reminder_type_daily']
            ],
            [
                ['text' => '🗓️ Еженедельное', 'callback_data' => 'reminder_type_weekly'],
                ['text' => '📆 Ежемесячное', 'callback_data' => 'reminder_type_monthly']
            ],
            [
                ['text' => '❌ Без напоминания', 'callback_data' => 'reminder_type_none']
            ]
        ];

        $message = "🔔 Выберите тип напоминания для задачи:\n";
        $message .= "\"*{$task->getTitle()}*\"\n\n";
        $message .= "💡 *Однократное* - напомнить один раз в указанное время\n";
        $message .= "💡 *Ежедневное* - напоминать каждый день\n";
        $message .= "💡 *Еженедельное* - напоминать раз в неделю\n";
        $message .= "💡 *Ежемесячное* - напоминать раз в месяц";

        $this->telegramService->sendKeyboard($chatId, $message, $buttons);
    }
}