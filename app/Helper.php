<?php

declare(strict_types=1);

namespace App;

use Dotenv\Dotenv;

class Helper
{
    public static function getEnv(): array
    {
        $dotenv = Dotenv::createArrayBacked(__DIR__.'/../');
        return $dotenv->load();
    }

    public static function consoleLog(string $message, string $type = 'INFO'): void {
        $colors = [
            'INFO' => "\033[36m",
            'SUCCESS' => "\033[32m",
            'ERROR' => "\033[31m",
            'WARNING' => "\033[33m"
        ];

        $reset = "\033[0m";
        $timestamp = date('Y-m-d H:i:s');

        echo "{$colors[$type]}[{$timestamp}] [{$type}]{$reset} {$message}\n";
    }
}