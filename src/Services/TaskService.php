<?php

namespace App\Services;

use App\Models\Task;
use App\Repositories\TaskRepository;

class TaskService
{
    private TaskRepository $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public function createTask(
        int $userId,
        string $title,
        ?string $description = null,
        ?string $dueDate = null,
        string $priority = Task::PRIORITY_MEDIUM
    ): ?Task {
        $task = new Task($userId, $title, $description, $dueDate, $priority);
        return $this->taskRepository->save($task);
    }

    /**
     * Обновляет задачу
     */
    public function updateTask(int $taskId, int $userId, array $data): ?Task
    {
        $task = $this->taskRepository->findById($taskId, $userId);
        
        if (!$task) {
            return null;
        }

        // Обновляем поля
        if (isset($data['title'])) {
            $task->setTitle($data['title']);
        }
        if (isset($data['description'])) {
            $task->setDescription($data['description']);
        }
        if (isset($data['due_date'])) {
            $task->setDueDate($data['due_date']);
        }
        if (isset($data['priority'])) {
            $task->setPriority($data['priority']);
        }
        if (isset($data['status'])) {
            $task->setStatus($data['status']);
        }

        return $this->taskRepository->save($task);
    }

    public function deleteTask(int $taskId, int $userId): bool
    {
        return $this->taskRepository->delete($taskId, $userId);
    }

    public function getUserTasks(int $userId): array
    {
        return $this->taskRepository->findByUserId($userId);
    }

    public function getTask(int $taskId, int $userId): ?Task
    {
        return $this->taskRepository->findById($taskId, $userId);
    }
}