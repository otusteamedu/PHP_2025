<?php

declare(strict_types=1);

namespace App\Validate\Message;

class MaxLengthMessage implements MessageInterface
{
    private int $limit;

    public function __construct(int $limit)
    {
        $this->limit = $limit;
    }

    public function getMessage(): string
    {
        return str_replace('{{ limit }}', (string)$this->limit, 'This value is too long. It should have {{ limit }} characters or less.');
    }
}
