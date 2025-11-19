<?php

namespace App\Base;

use Stringable;

class ErrorHandler
{
    protected const string LOG_ERRORS_PATH = __DIR__ . '/../logs/errors.log';

    public static function handleError($level, Stringable|string $message, string $file, int $line): void
    {
        $logger = new BaseLogger(self::LOG_ERRORS_PATH);
        $msg = "$level: $message $file:$line";
        $logger->critical($msg);
    }

    public static function handleException(Stringable|string $message): void
    {
        $logger = new BaseLogger(self::LOG_ERRORS_PATH);
        $logger->error($message);
        http_response_code(500);
    }
}