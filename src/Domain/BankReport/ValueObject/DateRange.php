<?php

declare(strict_types=1);

namespace App\Domain\BankReport\ValueObject;

use App\Domain\BankReport\Exception\DateRangeException;

readonly class DateRange
{
    public \DateTimeImmutable $dateFrom;
    public \DateTimeImmutable $dateTo;

    public function __construct(
        string $dateFrom,
        string $dateTo,
        ?\DateTimeImmutable $referenceTime = null,
    ) {
        $today = ($referenceTime ?? new \DateTimeImmutable())->setTime(0, 0);

        $dateFrom = $this->parseDate($dateFrom);
        $dateTo = $this->parseDate($dateTo);

        $this->validateDateRange($dateFrom, $dateTo);
        $this->validateFuture($dateFrom, $dateTo, $today);

        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    public static function defaultRange(?\DateTimeImmutable $referenceTime = null): self
    {
        $today = ($referenceTime ?? new \DateTimeImmutable())->setTime(0, 0);

        $firstOfPrevMonth = $today->modify('first day of previous month')->setTime(0, 0);
        $lastOfPrevMonth = $firstOfPrevMonth->modify('last day of this month')->setTime(0, 0);

        return new self(
            dateFrom: $firstOfPrevMonth->format('Y-m-d'),
            dateTo: $lastOfPrevMonth->format('Y-m-d'),
            referenceTime: $today,
        );
    }

    public function toArray(): array
    {
        return [
            'dateFrom' => $this->dateFrom->format('Y-m-d'),
            'dateTo' => $this->dateTo->format('Y-m-d'),
        ];
    }

    /**
     * @throws DateRangeException
     */
    private function parseDate(string $date): \DateTimeImmutable
    {
        $date = trim($date);

        if ($date === '') {
            throw DateRangeException::notEmpty();
        }

        try {
            return new \DateTimeImmutable($date)->setTime(0, 0);
        } catch (\DateMalformedStringException $e) {
            throw DateRangeException::invalidDate($date, $e->getMessage());
        }
    }

    /**
     * @throws DateRangeException
     */
    private function validateDateRange(
        \DateTimeImmutable $dateFrom,
        \DateTimeImmutable $dateTo,
    ): void {
        if ($dateTo < $dateFrom) {
            throw DateRangeException::fromIsAfterTo(
                $dateFrom->format('Y-m-d'),
                $dateTo->format('Y-m-d'),
            );
        }
    }

    /**
     * @throws DateRangeException
     */
    private function validateFuture(
        \DateTimeImmutable $dateFrom,
        \DateTimeImmutable $dateTo,
        \DateTimeImmutable $today,
    ): void {
        if ($dateFrom > $today) {
            throw DateRangeException::dateInFuture($dateFrom->format('Y-m-d'));
        }
        if ($dateTo > $today) {
            throw DateRangeException::dateInFuture($dateTo->format('Y-m-d'));
        }
    }
}
