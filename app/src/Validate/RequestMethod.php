<?php

declare(strict_types=1);

namespace App\Validate;

use App\Validate\Message\RequestMethodMessage;

class RequestMethod
{
    public function validate(string $method): ?string
    {
        $responseCode = new ResponseCode();

        if ($_SERVER['REQUEST_METHOD'] !== $method) {
            $responseCode->getHttpCode(405);
            $message = new RequestMethodMessage($method);
            return json_encode($message->getMessage());
        }
        $responseCode->getHttpCode(200);
        return null;
    }
}
