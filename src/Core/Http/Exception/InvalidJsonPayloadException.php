<?php

declare(strict_types=1);

namespace App\Core\Http\Exception;

use App\Core\Http\Message\HttpStatus;

class InvalidJsonPayloadException extends \InvalidArgumentException implements HttpExceptionInterface
{
    public function getHttpCode(): int
    {
        return HttpStatus::BadRequest->value;
    }
}
