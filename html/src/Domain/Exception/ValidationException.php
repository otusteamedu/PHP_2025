<?php

declare(strict_types=1);

namespace Otus\Queue\Domain\Exception;

use InvalidArgumentException;

final class ValidationException extends InvalidArgumentException
{
    /**
     * @param array $errors
     * @param string $message
     */
    public function __construct(
        private readonly array $errors,
        string $message = 'Validation failed',
    ) {
        parent::__construct($message);
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
