<?php

declare(strict_types=1);

namespace App\Core\Http\Exception;

use App\Core\Http\Message\HttpStatus;

class MissingQueryParamException extends \InvalidArgumentException implements HttpExceptionInterface
{
    public function __construct(
        private readonly string $param,
        string $message,
    ) {
        parent::__construct($message);
    }

    public function getHttpCode(): int
    {
        return HttpStatus::BadRequest->value;
    }

    public function getParam(): string
    {
        return $this->param;
    }
}
