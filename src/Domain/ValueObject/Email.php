<?php
declare(strict_types=1);

namespace Dinargab\Homework19\Domain\ValueObject;

class Email
{
    private string $email;
    public function __construct(string $email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception("Invalid email");
        }
        $this->email = $email;
    }

    public function getValue(): string
    {
        return $this->email;
    }
}