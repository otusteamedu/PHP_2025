<?php

namespace App\Exception;

use Exception;

class EmailInvalidException extends CustomException
{
    public function __construct(
        string $message = 'Email list is not valid',
        int $code = 400,
    ) {
        parent::__construct($message, $code);
    }
}
