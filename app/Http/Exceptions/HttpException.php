<?php
declare(strict_types=1);

namespace Zibrov\OtusPhp2025\Http\Exceptions;

use \Exception;

class HttpException extends Exception
{

    public function __construct(string $message = 'HTTP error', int $httpCode = 400)
    {
        parent::__construct($message, $httpCode);
    }

    public function getHttpCode(): int
    {
        return $this->getCode();
    }
}
