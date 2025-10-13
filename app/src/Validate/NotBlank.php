<?php

declare(strict_types=1);

namespace App\Validate;

use App\Validate\Message\NotBlankMessage;

class NotBlank
{
    public function validate(mixed $value): ?string
    {
        $responseCode = new ResponseCode();

        if (!isset($value)) {
            $responseCode->getHttpCode(422);
            $message = new NotBlankMessage();
            return json_encode($message->getMessage());
        }

        $responseCode->getHttpCode(200);
        return null;
    }
}
