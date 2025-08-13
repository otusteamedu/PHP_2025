<?php

namespace App\Domain\ValueObjects;

class EventName
{
    public function __construct(private string $value) {}

    public function toString()
    {
        return $this->value;
    }
}