<?php

declare(strict_types=1);

namespace App\Domain\Email;

final readonly class Email
{
    private string $value;
    private ?string $domain;

    public function __construct(string $value)
    {
        $normalizedValue = $this->normalize($value);
        $this->value = $normalizedValue;
        $this->domain = $this->extractDomain($normalizedValue);
    }

    /**
     * Возвращает нормализованное значение email
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Возвращает домен из email адреса
     */
    public function getDomain(): ?string
    {
        return $this->domain;
    }

    /**
     * Проверяет, является ли email пустым
     */
    public function isEmpty(): bool
    {
        return $this->value === '';
    }

    /**
     * Нормализует email адрес (убирает пробелы)
     */
    private function normalize(string $value): string
    {
        return trim($value);
    }

    /**
     * Извлекает домен из email адреса
     */
    private function extractDomain(string $email): ?string
    {
        $parts = explode('@', $email);
        if (count($parts) === 2) {
            return strtolower(trim($parts[1]));
        }
        
        return null;
    }
}
