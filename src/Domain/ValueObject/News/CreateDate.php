<?php

declare(strict_types=1);

namespace App\Domain\ValueObject\News;

final class CreateDate
{
    public function __construct(
        private \DateTimeImmutable $date,
    )
    {
        $this->assertDateIsValid($date);
    }

    public function getDate():\DateTimeImmutable
    {
        return $this->date;
    }

    private function assertDateIsValid(\DateTimeImmutable $value):void
    {
        if (!$value) {
            throw new \InvalidArgumentException('Date is invalid');
        }
    }
}
