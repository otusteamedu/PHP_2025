<?php

namespace App\Base;

use Psr\Log\AbstractLogger;
use Stringable;

class BaseLogger extends AbstractLogger
{
    public function __construct(protected ?string $path = null)
    {
        if (!$this->path) {
            $this->path = __DIR__ . '/../logs/application.log';
        }
    }

    public function log($level, Stringable|string $message, array $context = []): void
    {
        $message = $this->interpolate($message, $context);
        $messageString = '[' . date(DATE_RFC3339) . '] ' . strtoupper($level) . ': ' . $message . PHP_EOL;
        file_put_contents($this->path, $messageString, FILE_APPEND);
    }

    private function interpolate(string $message, array $context = []): string
    {
        $replace = [];
        foreach ($context as $key => $val) {
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            }
        }
        return strtr($message, $replace);
    }
}