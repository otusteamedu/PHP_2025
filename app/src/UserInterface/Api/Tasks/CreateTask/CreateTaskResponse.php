<?php

declare(strict_types=1);

namespace App\UserInterface\Api\Tasks\CreateTask;

class CreateTaskResponse
{
    public function __construct(
        public string $id,
    ){

    }

}
