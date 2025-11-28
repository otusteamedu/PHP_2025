<?php
declare(strict_types=1);

namespace Dinargab\Homework19\Domain\Console;

interface ConsoleLoggerInterface
{
    public function log(string $message): void;
}