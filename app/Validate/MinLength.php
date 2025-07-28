<?php

declare(strict_types=1);

namespace User\Php2025\Validate;

use User\Php2025\Validate\Message\MinLengthMessage;

class MinLength
{
    public function validate(string $value, int $minLength): ?string
    {
        if (mb_strlen($value, 'utf8') < $minLength) {
            http_response_code(400);
            $message = new MinLengthMessage($minLength);
            return json_encode($message->getMessage());
        }

        http_response_code(200);
        return null;
    }

}