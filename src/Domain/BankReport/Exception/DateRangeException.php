<?php

declare(strict_types=1);

namespace App\Domain\BankReport\Exception;

class DateRangeException extends \InvalidArgumentException
{
    public static function notEmpty(): self
    {
        return new self('Дата начала или окончания не может быть пустой.');
    }

    public static function invalidDate(string $value, string $details): self
    {
        return new self("Некорректная дата ($value): $details.");
    }

    public static function fromIsAfterTo(string $from, string $to): self
    {
        return new self("Дата начала ($from) не может быть позже даты окончания ($to).");
    }

    public static function dateInFuture(string $date): self
    {
        return new self("Дата в будущем ($date).");
    }
}
