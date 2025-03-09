<?php

namespace App\Exceptions;

use Exception;

class EmptyRequestException extends Exception
{
    public function getStatusCode(): int
    {
        return 422;
    }
}