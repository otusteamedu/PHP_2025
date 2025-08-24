<?php

namespace Larkinov\Myapp\Classes;

class Email
{
    
    public function __construct(private string $email) {}

    public function getEmail(): string
    {
        return $this->email;
    }
}
