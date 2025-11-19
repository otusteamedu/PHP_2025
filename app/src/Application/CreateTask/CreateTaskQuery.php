<?php

declare(strict_types=1);

namespace App\Application\CreateTask;

class CreateTaskQuery
{
    public function __construct(
        public string $email,
        public string $title,
        public string $description,
    ){

    }

}
