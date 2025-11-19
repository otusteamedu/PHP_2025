<?php

declare(strict_types=1);

namespace App\Application\GetTask;

use App\Domain\Task\TaskRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetTaskHandler
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
    )
    {

    }

    public function __invoke(GetTaskQuery $query): GetTaskOutput
    {
        $task = $this->taskRepository->findById($query->id);
        if($task === null){
            throw new NotFoundHttpException('Task not found');
        }

        return new GetTaskOutput(
            title: $task->getTitle(),
            description: $task->getDescription(),
            email: $task->getEmail(),
            status: $task->getStatus(),
        );
    }

}
