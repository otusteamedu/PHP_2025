<?php

namespace App\Domain\ValueObjects;

class Priority
{
    public function __construct(private int $value) {}

    public function toInt()
    {
        return $this->value;
    }
}