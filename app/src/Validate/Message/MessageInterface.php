<?php

declare(strict_types=1);

namespace App\Validate\Message;

interface MessageInterface
{
    public function getMessage(): string;
}
