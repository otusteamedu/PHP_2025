<?php

declare(strict_types=1);

namespace App\Presentation\Validation;

class ValidationException extends \Exception
{
    public function __construct(private readonly array $errors, string $message = "Invalid input.", int $code = 400)
    {
        parent::__construct($message, $code);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
