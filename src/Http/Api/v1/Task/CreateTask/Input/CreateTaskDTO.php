<?php
declare(strict_types=1);

namespace App\Http\Api\v1\Task\CreateTask\Input;

class CreateTaskDTO
{
    /**
     * @param array<string, mixed> $payload
     */
    public function __construct(
        public mixed $payload
    ) {}
}
