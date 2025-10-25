<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use DateTimeImmutable;
use InvalidArgumentException;

final readonly class DateRange
{
    public function __construct(
        private DateTimeImmutable $startDate,
        private DateTimeImmutable $endDate
    ) {
        if ($startDate > $endDate) {
            throw new InvalidArgumentException('Start date cannot be after end date');
        }
    }

    /**
     * Возвращает начальную дату диапазона
     */
    public function startDate(): DateTimeImmutable
    {
        return $this->startDate;
    }

    /**
     * Возвращает конечную дату диапазона
     */
    public function endDate(): DateTimeImmutable
    {
        return $this->endDate;
    }

    /**
     * Возвращает количество дней в диапазоне
     */
    public function daysCount(): int
    {
        return $this->endDate->diff($this->startDate)->days + 1;
    }

    /**
     * Проверяет, находится ли дата в пределах диапазона
     */
    public function isWithinRange(DateTimeImmutable $date): bool
    {
        return $date >= $this->startDate && $date <= $this->endDate;
    }

    /**
     * Форматирует диапазон дат в строку
     */
    public function format(string $format = 'Y-m-d'): string
    {
        return $this->startDate->format($format) . ' - ' . $this->endDate->format($format);
    }

    /**
     * Сравнивает два диапазона дат на равенство
     */
    public function equals(DateRange $other): bool
    {
        return $this->startDate == $other->startDate && $this->endDate == $other->endDate;
    }
}
