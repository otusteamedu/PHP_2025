<?php

declare(strict_types=1);

namespace App\Core\UserInterface\UserReport;

class Request
{
    public function __construct(
        public int $userId,
        public int $interval,
        public int $cardId,
        public string $email,
    ){

    }

}