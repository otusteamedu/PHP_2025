<?php

namespace App\Exception;

class HttpException extends \Exception implements IApplicationException
{

    public function getHttpCode(): int
    {
        return 400;
    }
}