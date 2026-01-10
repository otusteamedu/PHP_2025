<?php declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class ValidationException extends Exception
{
    /**
     * Массив ошибок валидации.
     *
     * @var array
     */
    private array $errors;

    /**
     * Создает новое исключение с массивом ошибок.
     *
     * @param array $errors
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct(string $message = "", array $errors = [], int $code = 400, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    /**
     * Возвращает массив ошибок валидации.
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Возвращает строковое представление исключения, включая ошибки.
     *
     * @return string
     */
    public function __toString(): string
    {
        $errorsString = implode(", ", array_map(
            static fn($field, $message) => "$field: $message",
            array_keys($this->errors),
            array_values($this->errors)
        ));
        return __CLASS__ . ": [{$this->code}]: {$this->message} (Errors: $errorsString)\n";
    }
}