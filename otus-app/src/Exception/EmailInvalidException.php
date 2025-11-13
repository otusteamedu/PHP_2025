<?php

declare(strict_types=1);

namespace App\Exception;

class EmailInvalidException extends CustomException
{
    public function __construct(
        string $message = 'Email list is not valid',
        int $code = 400,
    ) {
        parent::__construct($message, $code);
    }
}
