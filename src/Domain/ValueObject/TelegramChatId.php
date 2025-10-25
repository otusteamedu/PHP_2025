<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use InvalidArgumentException;

final class TelegramChatId
{
    public function __construct(private string $value)
    {
        if (empty($value)) {
            throw new InvalidArgumentException('Telegram chat ID cannot be empty');
        }

        if (!is_numeric($value)) {
            throw new InvalidArgumentException(
                'Invalid Telegram chat ID format. Must be a numeric ID'
            );
        }
    }

    /**
     * Возвращает значение id чата
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Возвращает числовое значение id чата
     */
    public function toInt(): int
    {
        return (int) $this->value;
    }

    /**
     * Сравнивает два идентификатора чата на равенство
     */
    public function equals(TelegramChatId $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * Возвращает строковое представление id чата
     */
    public function __toString(): string
    {
        return $this->value;
    }
}
