<?php

declare(strict_types=1);

namespace App\Application\DTO;

final readonly class EmailValidationResponse
{
    public function __construct(
        private string $formattedResult
    ) {}

    /**
     * Возвращает отформатированный результат валидации
     */
    public function getFormattedResult(): string
    {
        return $this->formattedResult;
    }
}
