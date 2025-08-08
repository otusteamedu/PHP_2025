<?php

declare(strict_types=1);

namespace App\UserInteface\Api\ReportNews\Response;

class Response
{
    public function __construct(
        public string $html,
    )
    {
    }
}

