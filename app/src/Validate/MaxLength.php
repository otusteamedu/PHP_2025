<?php

declare(strict_types = 1);

namespace User\Php2025\src\Validate;

use User\Php2025\src\Validate\Message\MaxLengthMessage;

class MaxLength
{
    public function validate(string $value, int $maxLength): ?string
    {
        $responseCode = new ResponseCode();

        if (mb_strlen($value, 'utf8') > $maxLength) {
            $responseCode->getHttpCode(400);
            $message = new MaxLengthMessage($maxLength);
            return json_encode($message->getMessage());
        }

        $responseCode->getHttpCode(200);
        return null;
    }
}
