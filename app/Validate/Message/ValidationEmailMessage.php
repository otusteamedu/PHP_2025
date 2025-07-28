<?php

namespace User\Php2025\Validate\Message;

class ValidationEmailMessage implements MessageInterface
{
    public function getMessage(): string
    {
        return 'This email is not valid.';
    }
}
