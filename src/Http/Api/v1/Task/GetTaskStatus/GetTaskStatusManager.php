<?php
declare(strict_types=1);

namespace App\Http\Api\v1\Task\GetTaskStatus;

use App\Application\UseCase\Task\GetTaskStatusHandler;
use App\Domain\Exception\ValidationException;
use App\Domain\Task\ValueObject\TaskId;
use App\Http\Api\v1\Task\GetTaskStatus\Input\GetTaskStatusDTO;
use App\Http\Api\v1\Task\GetTaskStatus\Output\TaskStatusDTO;
use Ramsey\Uuid\Uuid;

class GetTaskStatusManager
{
    public function __construct(
        private GetTaskStatusHandler $getTaskStatusHandler
    ) {}

    public function get(GetTaskStatusDTO $getTaskStatusDTO): TaskStatusDTO
    {
        $requestId = $getTaskStatusDTO->id;

        if ($requestId === '' || !Uuid::isValid($requestId)) {
            throw new ValidationException('request_id must be a valid UUID');
        }

        $task = $this->getTaskStatusHandler->handle(new TaskId($requestId));

        return new TaskStatusDTO(
            requestId: $task->getId()->toString(),
            status: $task->getStatus()->value,
        );
    }
}
