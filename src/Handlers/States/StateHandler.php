<?php

namespace App\Handlers\States;

use App\Services\TelegramService;
use App\Services\TaskService;
use App\Services\ReminderService;
use App\Repositories\UserStateRepository;
use App\Models\UserState;

class StateHandler
{
    private TelegramService $telegramService;
    private TaskService $taskService;
    private ReminderService $reminderService;
    private UserStateRepository $userStateRepository;

    public function __construct(
        TelegramService $telegramService,
        TaskService $taskService,
        ReminderService $reminderService,
        UserStateRepository $userStateRepository
    ) {
        $this->telegramService = $telegramService;
        $this->taskService = $taskService;
        $this->reminderService = $reminderService;
        $this->userStateRepository = $userStateRepository;
    }

    public function handle(UserState $userState, string $text, int $chatId, int $userId): void
    {
        switch ($userState->getState()) {
            case UserState::STATE_AWAITING_TASK_TITLE:
                $this->handleTaskTitleInput($userState, $text, $chatId, $userId);
                break;
            case UserState::STATE_AWAITING_TASK_DUE_DATE:
                $this->handleDueDateInput($userState, $text, $chatId, $userId);
                break;
            case UserState::STATE_AWAITING_REMINDER_DATE:
                $this->handleReminderDateInput($userState, $text, $chatId, $userId);
                break;
            case UserState::STATE_AWAITING_REMINDER_TIME:
                $this->handleReminderTimeInput($userState, $text, $chatId, $userId);
                break;
        }
    }

    private function handleTaskTitleInput(UserState $userState, string $title, int $chatId, int $userId): void
    {
        $userState->setTempDataValue('title', $title);
        $userState->setState(UserState::STATE_AWAITING_TASK_DUE_DATE);
        $this->userStateRepository->save($userState);

        $message = "📅 Теперь укажите срок выполнения задачи.\n\n";
        $message .= "Формат: ГГГГ-ММ-ДД\n";
        $message .= "Пример: " . date('Y-m-d', strtotime('+7 days')) . "\n\n";
        $message .= "💡 Используйте /cancel чтобы отменить создание";
        
        $this->telegramService->sendMessage($chatId, $message);
    }

    private function handleDueDateInput(UserState $userState, string $dueDate, int $chatId, int $userId): void
    {
        // Проверяем формат даты
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dueDate)) {
            $this->telegramService->sendMessage($chatId, "❌ Неверный формат даты. Используйте ГГГГ-ММ-ДД\nПример: " . date('Y-m-d'));
            return;
        }

        // Проверяем что дата валидна
        $dateParts = explode('-', $dueDate);
        if (count($dateParts) !== 3 || !checkdate((int)$dateParts[1], (int)$dateParts[2], (int)$dateParts[0])) {
            $this->telegramService->sendMessage($chatId, "❌ Неверная дата. Проверьте правильность ввода.\nПример: " . date('Y-m-d'));
            return;
        }

        // Проверяем что дата не в прошлом
        $inputDate = strtotime($dueDate);
        $today = strtotime(date('Y-m-d'));
        if ($inputDate < $today) {
            $this->telegramService->sendMessage($chatId, "❌ Дата не может быть в прошлом. Укажите сегодняшнюю или будущую дату.\nПример: " . date('Y-m-d'));
            return;
        }

        $userState->setTempDataValue('due_date', $dueDate);
        $userState->setState(UserState::STATE_AWAITING_TASK_PRIORITY);
        $this->userStateRepository->save($userState);

        $this->showPriorityKeyboard($chatId);
    }

    private function showPriorityKeyboard(int $chatId): void
    {
        $buttons = [
            [
                ['text' => '🔴 Высокий', 'callback_data' => 'priority_high'],
                ['text' => '🟡 Средний', 'callback_data' => 'priority_medium']
            ],
            [
                ['text' => '🟢 Низкий', 'callback_data' => 'priority_low']
            ]
        ];

        $message = "⚡ Выберите приоритет задачи:\n\n";
        $message .= "💡 Используйте /cancel чтобы отменить создание";
        
        $this->telegramService->sendKeyboard($chatId, $message, $buttons);
    }

    private function handleReminderDateInput(UserState $userState, string $reminderDate, int $chatId, int $userId): void
    {
        // Проверяем формат даты
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $reminderDate)) {
            $this->telegramService->sendMessage($chatId, "❌ Неверный формат даты. Используйте ГГГГ-ММ-ДД\nПример: " . date('Y-m-d'));
            return;
        }

        // Проверяем что дата валидна
        $dateParts = explode('-', $reminderDate);
        if (count($dateParts) !== 3 || !checkdate((int)$dateParts[1], (int)$dateParts[2], (int)$dateParts[0])) {
            $this->telegramService->sendMessage($chatId, "❌ Неверная дата. Проверьте правильность ввода.\nПример: " . date('Y-m-d'));
            return;
        }

        $userState->setTempDataValue('reminder_date', $reminderDate);
        $userState->setState(UserState::STATE_AWAITING_REMINDER_TIME);
        $this->userStateRepository->save($userState);

        $this->askForReminderTime($chatId);
    }

    private function askForReminderTime(int $chatId): void
    {
        $message = "⏰ Укажите время напоминания:\n\n";
        $message .= "Формат: *ЧЧ:ММ* (24-часовой формат)\n";
        $message .= "Пример: *09:00* или *18:30*\n\n";
        $message .= "💡 Напоминание придет в указанное время";

        $this->telegramService->sendMessage($chatId, $message);
    }

    private function handleReminderTimeInput(UserState $userState, string $reminderTime, int $chatId, int $userId): void
    {
        // Проверяем формат времени
        if (!preg_match('/^\d{2}:\d{2}$/', $reminderTime)) {
            $this->telegramService->sendMessage($chatId, "❌ Неверный формат времени. Используйте ЧЧ:ММ\nПример: 09:00 или 18:30");
            return;
        }

        // Проверяем что время валидно
        $timeParts = explode(':', $reminderTime);
        $hour = (int)$timeParts[0];
        $minute = (int)$timeParts[1];
        
        if ($hour < 0 || $hour > 23 || $minute < 0 || $minute > 59) {
            $this->telegramService->sendMessage($chatId, "❌ Неверное время. Часы: 00-23, Минуты: 00-59\nПример: 09:00 или 18:30");
            return;
        }

        $taskId = $userState->getTempDataValue('task_id');
        $reminderType = $userState->getTempDataValue('reminder_type');
        $reminderDate = $userState->getTempDataValue('reminder_date');

        $task = $this->taskService->getTask($taskId, $userId);
        if (!$task) {
            $this->telegramService->sendMessage($chatId, "❌ Задача не найдена.");
            return;
        }

        // Устанавливаем напоминание
        $success = $this->reminderService->setReminder($task, $reminderType, $reminderDate, $reminderTime);

        // Очищаем состояние пользователя
        $userState->setState(UserState::STATE_NONE);
        $userState->clearTempData();
        $this->userStateRepository->save($userState);

        if ($success) {
            $reminderTypeText = $this->getReminderTypeText($reminderType);
            $message = "✅ Напоминание установлено!\n\n";
            $message .= "📌 Задача: {$task->getTitle()}\n";
            $message .= "🔔 Тип: {$reminderTypeText}\n";
            $message .= "📅 Дата: {$reminderDate}\n";
            $message .= "⏰ Время: {$reminderTime}";
            
            $this->telegramService->sendMessage($chatId, $message);
        } else {
            $this->telegramService->sendMessage($chatId, "❌ Ошибка при установке напоминания.");
        }
    }

    private function getReminderTypeText(string $reminderType): string
    {
        return match($reminderType) {
            'one_time' => 'Однократное',
            'daily' => 'Ежедневное',
            'weekly' => 'Еженедельное',
            'monthly' => 'Ежемесячное',
            default => 'Без напоминания'
        };
    }
}