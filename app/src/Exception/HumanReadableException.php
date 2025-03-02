<?php

namespace App\Exception;

use Throwable;

class HumanReadableException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null, private readonly ?string $humanReadableException = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getHumanReadableException(): ?string
    {
        return $this->humanReadableException;
    }
}