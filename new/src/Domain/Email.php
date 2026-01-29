<?php

namespace EmailsVerifier\Domain;

readonly class Email
{
    private string $address;

    public function __construct(string $address)
    {
        $this->address = trim($address);
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function __toString(): string
    {
        return $this->address;
    }
}
