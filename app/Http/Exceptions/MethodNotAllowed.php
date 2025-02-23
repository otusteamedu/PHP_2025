<?php
declare(strict_types=1);

namespace Zibrov\OtusPhp2025\Http\Exceptions;

class MethodNotAllowed extends HttpException
{

    public function __construct(string $message = 'Method not allowed')
    {
        parent::__construct($message, 405);
    }
}
