<?php

declare(strict_types=1);

namespace User\Php2025\Validate;

use User\Php2025\Validate\Message\ValidationEmailMessage;

class ValidationEmail
{
    public function validate(string $email): ?string
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            $message = new ValidationEmailMessage();
            return json_encode($message->getMessage());
        }

        http_response_code(200);
        return null;
    }

}