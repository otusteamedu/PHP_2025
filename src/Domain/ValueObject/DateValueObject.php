<?php

declare(strict_types=1);

namespace Dinargab\Homework20\Domain\ValueObject;

use DateTimeImmutable;
use Dinargab\Homework20\Domain\Exception\InvalidDateException;

final class DateValueObject
{
    private DateTimeImmutable $date;

    /**
     * @throws InvalidDateException
     */
    public function __construct(string $dateString, string $format = 'Y-m-d')
    {
        $date = DateTimeImmutable::createFromFormat($format, $dateString);

        if ($date === false) {
            throw InvalidDateException::forInvalidFormat($dateString, $format);
        }

        $this->date = $date;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function equals(DateValueObject $other): bool
    {
        return $this->date == $other->date;
    }

    public function __toString(): string
    {
        return $this->date->format('Y-m-d');
    }
}
