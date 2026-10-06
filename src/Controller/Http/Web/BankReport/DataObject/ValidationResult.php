<?php

declare(strict_types=1);

namespace App\Controller\Http\Web\BankReport\DataObject;

readonly class ValidationResult
{
    private function __construct(
        private ?BankReportFormDto $dto,
        private array $errors,
    ) {
    }

    public static function ok(BankReportFormDto $dto): self
    {
        return new self($dto, []);
    }

    public static function withErrors(array $errors): self
    {
        return new self(null, $errors);
    }

    public function isValid(): bool
    {
        return $this->dto !== null;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getDto(): BankReportFormDto
    {
        return $this->dto ?? throw new \LogicException('DTO is not available when validation failed.');
    }
}
