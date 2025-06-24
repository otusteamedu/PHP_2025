<?php

namespace Producer\Exception;

use Exception;

class ValidationException extends Exception
{
    protected $message = "Ошибка валидации";
    protected $code = 422;
}