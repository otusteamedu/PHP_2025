<?php
declare(strict_types=1);

namespace Zibrov\OtusPhp2025\Validators;

use Zibrov\OtusPhp2025\Http\Exceptions\HttpException;

class ValidationException extends HttpException
{

    public function __construct(string $message = 'Validation failed', int $httpCode = 400)
    {
        parent::__construct($message, $httpCode);
    }
}
