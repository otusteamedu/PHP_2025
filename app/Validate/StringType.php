<?php

declare(strict_types=1);

namespace User\Php2025\Validate;

use User\Php2025\Validate\Message\StringTypeMessage;

class StringType
{
    public function validate(mixed $value): ?string
    {
        if (!is_string($value)) {
            http_response_code(400);
            $message = new StringTypeMessage();
            return json_encode($message->getMessage());
        }

        http_response_code(200);
        return null;
    }
}