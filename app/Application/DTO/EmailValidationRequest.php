<?php

declare(strict_types=1);

namespace App\Application\DTO;

final readonly class EmailValidationRequest
{
    public function __construct(
        private string $rawInput
    ) {}

    /**
     * Возвращает исходные данные запроса
     */
    public function getRawInput(): string
    {
        return $this->rawInput;
    }

    /**
     * Проверяет, является ли запрос пустым
     */
    public function isEmpty(): bool
    {
        return $this->rawInput === '';
    }
}
