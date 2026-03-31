<?php

declare(strict_types=1);

namespace App\Validate\Message;

class StringTypeMessage implements MessageInterface
{
    public function getMessage(): string
    {
        return 'The value must be a string.';
    }
}
