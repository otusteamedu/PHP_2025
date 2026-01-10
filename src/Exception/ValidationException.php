<?php declare(strict_types=1);

namespace App\Exception;

class ValidationException extends HttpException
{
    public function __construct(string $message = 'Validation failed')
    {
        parent::__construct($message, 400);
    }
}
