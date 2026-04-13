<?php

declare(strict_types=1);

namespace App\Decorator;

final class LoggingNotifierDecorator extends EmailNotifierDecorator
{
    public function __construct(EmailNotifier $wrapped, private string $logPath)
    {
        parent::__construct($wrapped);
    }

    public function send(string $to, string $subject, string $body): bool
    {
        $result = parent::send($to, $subject, $body);

        $line = sprintf(
            "[%s] to=%s subject=%s status=%s%s",
            date('Y-m-d H:i:s'),
            $to,
            $subject,
            $result ? 'SENT' : 'FAILED',
            PHP_EOL
        );

        file_put_contents($this->logPath, $line, FILE_APPEND);

        return $result;
    }
}
