<?php

namespace App\Handlers;

use App\Services\TelegramService;
use App\Services\TaskService;
use App\Services\ReminderService;
use App\Repositories\UserStateRepository;
use App\Models\UserState;

class CallbackQueryHandler
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

    public function handle($callbackQuery): void
    {
        $data = $callbackQuery->getData();
        $userId = $callbackQuery->getFrom()->getId();
        $chatId = $callbackQuery->getMessage()->getChat()->getId();
        $messageId = $callbackQuery->getMessage()->getMessageId();

        $userState = $this->userStateRepository->findByUserId($userId) ?? new UserState($userId);

        if (str_starts_with($data, 'priority_')) {
            $priority = str_replace('priority_', '', $data);
            $this->handlePrioritySelection($userState, $priority, $chatId, $userId);
        }

        if (str_starts_with($data, 'reminder_type_')) {
            $reminderType = str_replace('reminder_type_', '', $data);
            $this->handleReminderTypeSelection($userState, $reminderType, $chatId, $userId);
        }
    }

    private function handlePrioritySelection(UserState $userState, string $priority, int $chatId, int $userId): void
    {
        $title = $userState->getTempDataValue('title');
        $dueDate = $userState->getTempDataValue('due_date');

        // Создаем задачу
        $task = $this->taskService->createTask($userId, $title, null, $dueDate, $priority);

        // Очищаем состояние пользователя
        $userState->setState(UserState::STATE_NONE);
        $userState->clearTempData();
        $this->userStateRepository->save($userState);

        if ($task) {
            $message = "✅ Задача создана!\n\n";
            $message .= "📌 Название: {$task->getTitle()}\n";
            $message .= "📅 Срок: {$task->getDueDate()}\n";
            $message .= "⚡ Приоритет: " . $this->getPriorityText($task->getPriority()) . "\n";
            $message .= "🆔 ID: {$task->getId()}";
            
            $this->telegramService->removeKeyboard($chatId, $message);
        } else {
            $this->telegramService->sendMessage($chatId, "❌ Ошибка при создании задачи.");
        }
    }

    private function handleReminderTypeSelection(UserState $userState, string $reminderType, int $chatId, int $userId): void
    {
        $taskId = $userState->getTempDataValue('task_id');
        $task = $this->taskService->getTask($taskId, $userId);

        if (!$task) {
            $this->telegramService->sendMessage($chatId, "❌ Задача не найдена.");
            return;
        }

        if ($reminderType === 'none') {
            $success = $this->reminderService->removeReminder($task);
            $message = $success ? "✅ Напоминание удалено." : "❌ Ошибка при удалении напоминания.";
            $this->telegramService->removeKeyboard($chatId, $message);
            
            $userState->setState(UserState::STATE_NONE);
            $userState->clearTempData();
            $this->userStateRepository->save($userState);
            return;
        }

        // Для типов кроме 'none' запрашиваем дату и время
        $userState->setTempDataValue('reminder_type', $reminderType);
        $userState->setState(UserState::STATE_AWAITING_REMINDER_DATE);
        $this->userStateRepository->save($userState);

        $this->askForReminderDate($chatId, $reminderType);
    }

    private function askForReminderDate(int $chatId, string $reminderType): void
    {
        $examples = [
            'one_time' => date('Y-m-d'),
            'daily' => date('Y-m-d'),
            'weekly' => date('Y-m-d'),
            'monthly' => date('Y-m-d')
        ];

        $message = "📅 Укажите дату начала напоминания:\n\n";
        $message .= "Формат: *ГГГГ-ММ-ДД*\n";
        $message .= "Пример: *{$examples[$reminderType]}*\n\n";
        $message .= "💡 Для ежедневных напоминаний укажите, с какой даты начать";

        $this->telegramService->sendMessage($chatId, $message);
    }

    private function getPriorityText(string $priority): string
    {
        return match($priority) {
            'high' => 'Высокий 🔴',
            'medium' => 'Средний 🟡',
            'low' => 'Низкий 🟢',
            default => 'Неизвестный'
        };
    }
}