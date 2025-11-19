<?php

namespace App\Services;

use App\Models\Task;
use App\Repositories\TaskRepository;
use App\Services\TelegramService;

class ReminderService
{
    private TaskRepository $taskRepository;
    private TelegramService $telegramService;

    public function __construct(TaskRepository $taskRepository, TelegramService $telegramService)
    {
        $this->taskRepository = $taskRepository;
        $this->telegramService = $telegramService;
    }

    /**
     * Отправка напоминаний, которые должны быть отправлены сейчас
     */
    public function sendDueReminders(): void
    {
        $currentDateTime = date('Y-m-d H:i:00');
        error_log("🔔 Checking reminders for: {$currentDateTime}");

        // Находим задачи с напоминаниями, которые должны быть отправлены
        $tasksToRemind = $this->findTasksForReminding($currentDateTime);

        foreach ($tasksToRemind as $task) {
            $this->sendReminder($task);
            $this->updateNextReminder($task);
        }

        error_log("🔔 Sent reminders for " . count($tasksToRemind) . " tasks");
    }

    /**
     * Находит задачи для напоминания
     */

    private function findTasksForReminding(string $currentDateTime): array
    {
        error_log("🔔 Finding tasks for reminding at: {$currentDateTime}");
        
        try {
            $tasks = $this->taskRepository->findTasksForReminder($currentDateTime);
            error_log("🔔 Repository returned " . count($tasks) . " tasks");
            
            // Дополнительная отладка: посмотрим все задачи с напоминаниями
            $allTasks = $this->taskRepository->findAllTasksWithReminders();
            error_log("🔔 Total tasks with reminders: " . count($allTasks));
            
            foreach ($allTasks as $task) {
                error_log("🔔 Task #{$task->getId()}: {$task->getTitle()}, Type: {$task->getReminderType()}, Time: {$task->getReminderTime()}");
            }
            
            return $tasks;
        } catch (\Exception $e) {
            error_log("❌ Error in findTasksForReminding: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Проверяет, нужно ли отправлять напоминание для задачи
     */
    private function shouldSendReminder(Task $task, string $currentDateTime): bool
    {
        if (!$task->hasReminder()) {
            return false;
        }

        $nextReminder = $task->getNextReminderDateTime();
        if (!$nextReminder) {
            return false;
        }

        // Для разовых напоминаний проверяем точное время
        if ($task->getReminderType() === Task::REMINDER_TYPE_ONE_TIME) {
            return $nextReminder === $currentDateTime;
        }

        // Для повторяющихся напоминаний проверяем по расписанию
        return $this->checkRecurringReminder($task, $currentDateTime);
    }

    /**
     * Проверяет повторяющиеся напоминания
     */
    private function checkRecurringReminder(Task $task, string $currentDateTime): bool
    {
        $currentTime = date('H:i:00');
        $taskTime = $task->getReminderTime();

        if ($taskTime !== $currentTime) {
            return false;
        }

        $currentDate = date('Y-m-d');
        $lastReminder = $task->getLastReminderSent();

        // Если напоминание еще не отправлялось сегодня
        if (!$lastReminder || date('Y-m-d', strtotime($lastReminder)) !== $currentDate) {
            switch ($task->getReminderType()) {
                case Task::REMINDER_TYPE_DAILY:
                    return true;

                case Task::REMINDER_TYPE_WEEKLY:
                    return date('w') === date('w', strtotime($task->getReminderDate()));

                case Task::REMINDER_TYPE_MONTHLY:
                    return date('d') === date('d', strtotime($task->getReminderDate()));
            }
        }

        return false;
    }

    /**
     * Отправляет напоминание пользователю
     */
    private function sendReminder(Task $task): void
    {
        $message = $this->buildReminderMessage($task);
        
        try {
            $this->telegramService->sendMessage($task->getUserId(), $message);
            error_log("✅ Reminder sent for task: {$task->getTitle()} to user: {$task->getUserId()}");
        } catch (\Exception $e) {
            error_log("❌ Failed to send reminder: " . $e->getMessage());
        }
    }

    /**
     * Строит сообщение напоминания
     */
    private function buildReminderMessage(Task $task): string
    {
        $priorityEmoji = $this->getPriorityEmoji($task->getPriority());
        $reminderType = $this->getReminderTypeText($task->getReminderType());

        $message = "🔔 *НАПОМИНАНИЕ*\n\n";
        $message .= "📌 *Задача:* {$task->getTitle()}\n";
        $message .= "⚡ *Приоритет:* {$priorityEmoji}\n";
        $message .= "🔄 *Тип:* {$reminderType}\n";

        if ($task->getDueDate()) {
            $dueDate = date('d.m.Y', strtotime($task->getDueDate()));
            $message .= "📅 *Срок:* {$dueDate}\n";
        }

        if ($task->getDescription()) {
            $message .= "\n📝 *Описание:* {$task->getDescription()}\n";
        }

        $message .= "\n💡 Не забудьте выполнить задачу!";

        return $message;
    }

    /**
     * Обновляет следующее напоминание для повторяющихся задач
     */
    private function updateNextReminder(Task $task): void
    {
        if ($task->isRecurringReminder()) {
            $task->setLastReminderSent(date('Y-m-d H:i:s'));
            $this->taskRepository->save($task);
        }
    }

    /**
     * Устанавливает напоминание для задачи
     */
    public function setReminder(Task $task, string $reminderType, ?string $reminderDate = null, ?string $reminderTime = null): bool
    {
        $task->setReminderType($reminderType);
        $task->setReminderDate($reminderDate);
        $task->setReminderTime($reminderTime);

        return $this->taskRepository->save($task) !== null;
    }

    /**
     * Удаляет напоминание у задачи
     */
    public function removeReminder(Task $task): bool
    {
        $task->setReminderType(Task::REMINDER_TYPE_NONE);
        $task->setReminderDate(null);
        $task->setReminderTime(null);
        $task->setLastReminderSent(null);

        return $this->taskRepository->save($task) !== null;
    }

    private function getPriorityEmoji(string $priority): string
    {
        return match($priority) {
            Task::PRIORITY_HIGH => '🔴 Высокий',
            Task::PRIORITY_MEDIUM => '🟡 Средний',
            Task::PRIORITY_LOW => '🟢 Низкий',
            default => '⚪ Неизвестный'
        };
    }

    private function getReminderTypeText(string $reminderType): string
    {
        return match($reminderType) {
            Task::REMINDER_TYPE_ONE_TIME => 'Однократное',
            Task::REMINDER_TYPE_DAILY => 'Ежедневное',
            Task::REMINDER_TYPE_WEEKLY => 'Еженедельное',
            Task::REMINDER_TYPE_MONTHLY => 'Ежемесячное',
            default => 'Без напоминания'
        };
    }
}