<?php

namespace App\Validate\Message;

class RequestMethodMessage implements MessageInterface
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
