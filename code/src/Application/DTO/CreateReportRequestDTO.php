<?php

declare(strict_types=1);

namespace App\Application\DTO;

class CreateReportRequestDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly int $year
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: trim($data['name'] ?? ''),
            email: trim($data['email'] ?? ''),
            year: (int)($data['year'] ?? 0)
        );
    }

    public function validate(): array
    {
        $errors = [];

        if (empty($this->name)) {
            $errors['name'] = 'Имя обязательно для заполнения';
        }

        if (empty($this->email)) {
            $errors['email'] = 'Email обязателен для заполнения';
        } elseif (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Некорректный формат email';
        }

        $currentYear = (int)date('Y');
        if ($this->year < 2000 || $this->year > $currentYear) {
            $errors['year'] = "Год должен быть от 2000 до {$currentYear}";
        }

        return $errors;
    }
}
