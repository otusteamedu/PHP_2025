<?php

declare(strict_types=1);

namespace App\Validate;

class ResponseCode
{
    public function getHttpCode(int $code): bool|int
    {
        return http_response_code($code);
    }
}
