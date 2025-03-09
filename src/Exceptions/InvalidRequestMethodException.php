<?php

namespace App\Exceptions;

use Exception;

class InvalidRequestMethodException extends Exception
{
    public function getStatusCode(): int
    {
        return 405;
    }
}