<?php

declare(strict_types=1);

namespace App\UserInterface\Api\Tasks\GetTask;

class GetTaskResponse
{
    public function __construct(
        public string $title,
        public string $description,
        public string $email,
        public string $status,
    )
    {

    }
}
