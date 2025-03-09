<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class EmailValidationException extends Exception
{
    /**
     * @var array
     */
    private array $errors;

    /**
     * @param array $errors
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct(
        string $message = "",
        array $errors = [],
        int $code = 400,
        ?Exception $previous = null
    )
    {
        parent::__construct($message, $code, $previous);

        $this->errors = $errors;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return 422;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}