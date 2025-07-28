<?php

declare(strict_types=1);

namespace User\Php2025\Validate\Message;

class NotBlankMessage implements MessageInterface
{
    public function getMessage(): string
    {
        return 'This value should not be blank.';
    }
}