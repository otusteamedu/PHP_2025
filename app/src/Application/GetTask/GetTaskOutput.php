<?php

declare(strict_types=1);

namespace App\Application\GetTask;

class GetTaskOutput
{
    public function __construct(
        public string $title,
        public string $description,
        public string $email,
        public string $status,
    ){

    }

}
