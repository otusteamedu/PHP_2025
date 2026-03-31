<?php

declare(strict_types=1);

namespace App\Validate;

use App\Validate\Message\StringTypeMessage;

class StringType
{
    public function validate(mixed $value): ?string
    {
        $responseCode = new ResponseCode();

        if (!is_string($value)) {
            $responseCode->getHttpCode(400);
            $message = new StringTypeMessage();
            return json_encode($message->getMessage());
        }

        $responseCode->getHttpCode(200);
        return null;
    }
}
