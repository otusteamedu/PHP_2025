<?php
declare(strict_types=1);

namespace App\Http\Api\v1\Task\CreateTask;

use App\Application\UseCase\Task\CreateTaskHandler;
use App\Domain\Exception\ValidationException;
use App\Http\Api\v1\Task\CreateTask\Input\CreateTaskDTO;
use App\Http\Api\v1\Task\CreateTask\Output\CreatedTaskDTO;

class CreateTaskManager
{
    public function __construct(
        private CreateTaskHandler $createTaskHandler
    ) {}

    public function create(CreateTaskDTO $createTaskDTO): CreatedTaskDTO
    {
        $payload = $createTaskDTO->payload;

        if ($payload === null) {
            $payload = [];
        }

        if (!is_array($payload)) {
            throw new ValidationException('Request body must be a JSON object');
        }

        $task = $this->createTaskHandler->handle($payload);

        return new CreatedTaskDTO(
            requestId: $task->getId()->toString(),
            status: $task->getStatus()->value,
        );
    }
}
