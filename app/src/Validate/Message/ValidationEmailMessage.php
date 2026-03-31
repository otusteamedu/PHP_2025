<?php

namespace App\Validate\Message;

class ValidationEmailMessage implements MessageInterface
{
    public function getMessage(): string
    {
        return 'This email is not valid.';
    }
}
