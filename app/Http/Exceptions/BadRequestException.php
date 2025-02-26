<?php
declare(strict_types=1);

namespace Zibrov\OtusPhp2025\Http\Exceptions;

class BadRequestException extends HttpException
{

    public function __construct(string $message = 'Bad Request')
    {
        parent::__construct($message, 400);
    }
}