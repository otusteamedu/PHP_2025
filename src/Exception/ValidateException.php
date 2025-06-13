<?php

namespace App\Exception;

class ValidateException extends \Exception implements IApplicationException
{

    public function getHttpCode(): int
    {
        return 400;
    }
}