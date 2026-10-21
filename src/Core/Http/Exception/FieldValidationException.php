<?php

declare(strict_types=1);

namespace App\Core\Http\Exception;

class FieldValidationException extends \InvalidArgumentException implements HttpExceptionInterface
{
    public function __construct(
        private readonly string $field,
        string $message,
        int $httpCode = 400,
    ) {
        parent::__construct($message, $httpCode);
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getHttpCode(): int
    {
        return $this->getCode();
    }
}
