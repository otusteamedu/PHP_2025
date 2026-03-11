<?php
declare(strict_types=1);

namespace App\Http\Api\v1\Task\GetTaskStatus\Input;

class GetTaskStatusDTO
{
    public function __construct(
        public string $id
    ) {}
}
