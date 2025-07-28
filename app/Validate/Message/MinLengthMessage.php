<?php

namespace User\Php2025\Validate\Message;

class MinLengthMessage implements MessageInterface
{
    private int $limit;

    public function __construct(int $limit)
    {
        $this->limit = $limit;
    }

    public function getMessage(): string
    {
        return str_replace('{{ limit }}', (string)$this->limit, 'This value is too short. It should have {{ limit }} characters or more.');
    }
}
