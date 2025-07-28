<?php

declare(strict_types=1);

namespace User\Php2025\Validate;

use User\Php2025\Validate\Message\NotBlankMessage;

class NotBlank
{
    public function validate(mixed $value): ?string
    {
        if (!isset($value)) {
            http_response_code(422);
            $message = new NotBlankMessage();
            return json_encode($message->getMessage());
        }

        http_response_code(200);
        return null;
    }

}