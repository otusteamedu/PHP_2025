<?php

declare(strict_types=1);

namespace App\Core\Http\Exception;

use App\Core\Http\Message\HttpStatus;

class FieldValidationException extends \InvalidArgumentException implements HttpExceptionInterface
{
    public function __construct(
        private readonly string $field,
        string $message,
    ) {
        parent::__construct($message);
    }

    public function getHttpCode(): int
    {
        return HttpStatus::UnprocessableContent->value;
    }

    public function getField(): string
    {
        return $this->field;
    }
}
