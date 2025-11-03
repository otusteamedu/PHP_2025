<?php

namespace App\Core\Application;

class UserReportQuery
{
    public function __construct(
        public int $userId,
        public int $interval,
        public int $cardId,
        public string $email,
    )
    {
    }
}
