<?php

namespace App\Domain\ValueObjects;

class Priority
{
    public function __construct(private int $value) {}

    public function toInt(): int
    {
        return $this->value;
    }
}