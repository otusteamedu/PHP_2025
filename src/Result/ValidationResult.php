<?php

declare(strict_types=1);

namespace Dinargab\Homework5\Result;


class ValidationResult implements ResultInterface
{
    private string $inputValue;
    private bool $isValid;
    private ?string $error = null;

    public function __construct(string $inputValue, bool $isValid, ?string $error = null)
    {
        $this->inputValue = $inputValue;
        $this->isValid = $isValid;
        $this->error = $error;
    }

    public function getInputValue(): string
    {
        return $this->inputValue;
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    /**
     * Возвращает результат в виде ассоциативного массива
     *
     * @return array{inputValue: string, valid: bool, error: string|null}
     */
    public function toArray(): array
    {
        return [
            'inputValue' => $this->inputValue,
            'valid' => $this->isValid,
            'error' => $this->error,
        ];
    }
}