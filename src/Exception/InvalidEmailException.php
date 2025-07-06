<?php

namespace App\Exception;

class InvalidEmailException extends \InvalidArgumentException implements IApplicationException
{
    public function __construct(string $email) {
        parent::__construct("Некорректный формат email: '$email'");
    }

    public function getHttpCode(): int
    {
        return 400;
    }
}