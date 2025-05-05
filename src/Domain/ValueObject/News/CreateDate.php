<?php

declare(strict_types=1);

namespace App\Domain\ValueObject\News;

final class CreateDate
{
    public function __construct(
        private \DateTimeInterface $date,
    )
    {
        $this->assertDateIsValid($date);
    }

    public function getDate():\DateTimeInterface
    {
        return $this->date;
    }

    private function assertDateIsValid(\DateTimeInterface $value):void
    {
        if (!$value) {
            throw new \InvalidArgumentException('Date is invalid');
        }
    }
}
