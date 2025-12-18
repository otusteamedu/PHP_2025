<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

final class Number
{
    private int $number;

    public function __construct(string $number)
    {
        $this->setNumber($number);
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    private function setNumber(string $number): void
    {
        if (!is_numeric($number)) {
            throw new \InvalidArgumentException('This value should be numeric.');
        }

        $this->number = (int)$number;
    }
}
