<?php

namespace App\Shared\Validate;

class RequestMethodMessage
{
    private string $method;

    public function __construct(string $method)
    {
        $this->method = $method;
    }

    public function getMessage(): string
    {
        return str_replace('{{ method }}', $this->method, 'This method allow only method {{ method }}.');
    }
}