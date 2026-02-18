<?php

declare(strict_types=1);

namespace User\Php2025\src\Validate;

use User\Php2025\src\Validate\Message\MinLengthMessage;

class MinLength
{
    public function validate(string $value, int $minLength): ?string
    {
        $responseCode = new ResponseCode();

        if (mb_strlen($value, 'utf8') < $minLength) {
            $responseCode->getHttpCode(400);
            $message = new MinLengthMessage($minLength);
            return json_encode($message->getMessage());
        }

        $responseCode->getHttpCode(200);
        return null;
    }
}
