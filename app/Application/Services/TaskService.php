<?php

namespace App\Application\Services;

use App\Infrastructure\Queue\RabbitmqSender;
use App\Infrastructure\Repositories\TaskRepository;

class TaskService
{
    public function __construct(
        private TaskRepository $repository,
        private RabbitmqSender $worker
    ) {}

    public function sendMessage(array $data): int
    {
        $taskId = $this->repository->add($data['user_id']);
        $data['task_id'] = $taskId;

        $message = json_encode($data);
        $this->worker->sendMessage($message);

        return $taskId;
    }

    public function completeTask(int $taskId, int $userId): void
    {
        $this->repository->complete($taskId, $userId);
    }

    public function getStatusById(int $taskId, int $userId): ?string
    {
        $status = $this->repository->getStatusById($taskId, $userId);
        return $status;
    }
}