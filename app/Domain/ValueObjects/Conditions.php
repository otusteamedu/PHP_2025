<?php

namespace App\Domain\ValueObjects;

class Conditions
{
    public function __construct(private array $value) {}

    public function toArray()
    {
        return $this->value;
    }
}