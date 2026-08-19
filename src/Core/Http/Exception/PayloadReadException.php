<?php

declare(strict_types=1);

namespace App\Core\Http\Exception;

class PayloadReadException extends \InvalidArgumentException implements HttpExceptionInterface
{
    public function __construct(string $message, int $code = 400)
    {
        parent::__construct($message, $code);
    }

    public function getHttpCode(): int
    {
        return $this->getCode();
    }
}
