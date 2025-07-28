<?php

declare(strict_types = 1);

namespace User\Php2025\Validate;

use User\Php2025\Validate\Message\MaxLengthMessage;

class MaxLength
{
    public function validate(string $value, int $maxLength): ?string
    {
        if (mb_strlen($value, 'utf8') > $maxLength) {
            http_response_code(400);
            $message = new MaxLengthMessage($maxLength);
            return json_encode($message->getMessage());
        }

        http_response_code(200);
        return null;
    }


}