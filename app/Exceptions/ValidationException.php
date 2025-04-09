<?php

namespace App\Exceptions;

use Exception;

class ValidationException extends Exception
{
    protected $message = "Произошла валидационная ошибка";
    protected $code = 422;
}