<?php

namespace App\Exceptions;

class ValidationException extends \Exception
{
    protected $code = 400;
}