<?php

namespace Domain\ValueObject;

class Url
{
    private string $value;

    public function __construct(string $value)
    {
        $this->assertValidName($value);
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    private function assertValidName(string $value): void
    {
        if(! @ file_get_contents($value)){
            throw new \InvalidArgumentException("URL {$value} не сущесвует!");
        }
    }
}