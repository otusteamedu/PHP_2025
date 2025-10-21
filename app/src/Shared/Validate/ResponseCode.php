<?php

namespace App\Shared\Validate;

class ResponseCode
{
    public function getHttpCode(int $code): bool|int
    {
        return http_response_code($code);
    }
}