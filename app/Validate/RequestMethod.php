<?php

declare(strict_types=1);

namespace User\Php2025\Validate;

use User\Php2025\Validate\Message\RequestMethodMessage;

class RequestMethod
{
    public function validate(string $method): ?string
    {
        if ($_SERVER['REQUEST_METHOD'] !== $method) {
            http_response_code(405);
            $message = new RequestMethodMessage($method);
            return json_encode($message->getMessage());
        }
        http_response_code(200);
        return null;
    }
}
