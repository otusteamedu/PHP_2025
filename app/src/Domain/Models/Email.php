<?php

namespace Pryaniki\App\Domain\Models;

class Email
{
    private string $value;

    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->value = $value ? : '';
    }

    public function getDomain(): string
    {
        return array_last(explode('@', $this->value));
    }
}