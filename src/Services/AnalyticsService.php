<?php

namespace App\Services;

use App\Repositories\TaskRepository;
use App\Models\Task;

class AnalyticsService
{
    private TaskRepository $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    /**
     * Получает статистику для пользователя
     */
    public function getUserStatistics(int $userId): array
    {
        $allTasks = $this->taskRepository->findByUserId($userId);
        
        if (empty($allTasks)) {
            return $this->getEmptyStatistics();
        }

        return [
            'basic' => $this->getBasicStatistics($allTasks),
            'priority' => $this->getPriorityStatistics($allTasks),
            'due_dates' => $this->getDueDateStatistics($allTasks),
            'completion_trend' => $this->getCompletionTrend($allTasks)
        ];
    }

    /**
     * Базовая статистика
     */
    private function getBasicStatistics(array $tasks): array
    {
        $total = count($tasks);
        $completed = array_filter($tasks, fn($task) => $task->getStatus() === Task::STATUS_COMPLETED);
        $pending = array_filter($tasks, fn($task) => $task->getStatus() === Task::STATUS_PENDING);
        
        $completedCount = count($completed);
        $pendingCount = count($pending);
        $completionRate = $total > 0 ? round(($completedCount / $total) * 100) : 0;

        return [
            'total' => $total,
            'completed' => $completedCount,
            'pending' => $pendingCount,
            'completion_rate' => $completionRate
        ];
    }

    /**
     * Статистика по приоритетам
     */
    private function getPriorityStatistics(array $tasks): array
    {
        $priorities = [
            Task::PRIORITY_HIGH => ['total' => 0, 'completed' => 0],
            Task::PRIORITY_MEDIUM => ['total' => 0, 'completed' => 0],
            Task::PRIORITY_LOW => ['total' => 0, 'completed' => 0]
        ];

        foreach ($tasks as $task) {
            $priority = $task->getPriority();
            if (isset($priorities[$priority])) {
                $priorities[$priority]['total']++;
                if ($task->getStatus() === Task::STATUS_COMPLETED) {
                    $priorities[$priority]['completed']++;
                }
            }
        }

        return $priorities;
    }

    /**
     * Статистика по срокам
     */
    private function getDueDateStatistics(array $tasks): array
    {
        $today = date('Y-m-d');
        $overdue = 0;
        $todayDue = 0;
        $futureDue = 0;
        $noDueDate = 0;

        foreach ($tasks as $task) {
            if ($task->getStatus() === Task::STATUS_COMPLETED) {
                continue;
            }

            $dueDate = $task->getDueDate();
            
            if (!$dueDate) {
                $noDueDate++;
            } elseif ($dueDate < $today) {
                $overdue++;
            } elseif ($dueDate === $today) {
                $todayDue++;
            } else {
                $futureDue++;
            }
        }

        return [
            'overdue' => $overdue,
            'today' => $todayDue,
            'future' => $futureDue,
            'no_due_date' => $noDueDate
        ];
    }

    /**
     * Тренд выполнения (последние 7 дней)
     */
    private function getCompletionTrend(array $tasks): array
    {
        $last7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $last7Days[$date] = 0;
        }

        foreach ($tasks as $task) {
            if ($task->getStatus() === Task::STATUS_COMPLETED) {
                $completedDate = date('Y-m-d', strtotime($task->getUpdatedAt()));
                if (isset($last7Days[$completedDate])) {
                    $last7Days[$completedDate]++;
                }
            }
        }

        return $last7Days;
    }

    /**
     * Статистика для пользователя без задач
     */
    private function getEmptyStatistics(): array
    {
        return [
            'basic' => [
                'total' => 0,
                'completed' => 0,
                'pending' => 0,
                'completion_rate' => 0
            ],
            'priority' => [
                Task::PRIORITY_HIGH => ['total' => 0, 'completed' => 0],
                Task::PRIORITY_MEDIUM => ['total' => 0, 'completed' => 0],
                Task::PRIORITY_LOW => ['total' => 0, 'completed' => 0]
            ],
            'due_dates' => [
                'overdue' => 0,
                'today' => 0,
                'future' => 0,
                'no_due_date' => 0
            ],
            'completion_trend' => array_fill_keys($this->getLast7Days(), 0)
        ];
    }

    private function getLast7Days(): array
    {
        $days = [];
        for ($i = 6; $i >= 0; $i--) {
            $days[] = date('Y-m-d', strtotime("-$i days"));
        }
        return $days;
    }
}