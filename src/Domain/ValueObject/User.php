<?php

namespace Domain\ValueObject;

class User
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
        if(!is_numeric($value)){
            throw new \InvalidArgumentException("Пользователь {$value} не сущесвует!");
        }
    }
}