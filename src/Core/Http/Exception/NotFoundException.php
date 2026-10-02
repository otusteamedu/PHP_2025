<?php

declare(strict_types=1);

namespace App\Core\Http\Exception;

class NotFoundException extends \RuntimeException implements HttpExceptionInterface
{
    public function __construct(string $message, int $httpCode = 404)
    {
        parent::__construct($message, $httpCode);
    }

    public function getHttpCode(): int
    {
        return $this->getCode();
    }
}
