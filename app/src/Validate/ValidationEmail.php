<?php

declare(strict_types=1);

namespace App\Validate;

use App\Validate\Message\ValidationEmailMessage;

class ValidationEmail
{
    public function validate(string $email): ?string
    {
        $responseCode = new ResponseCode();

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $responseCode->getHttpCode(400);
            $message = new ValidationEmailMessage();
            return json_encode($message->getMessage());
        }

        $responseCode->getHttpCode(200);
        return null;
    }
}
