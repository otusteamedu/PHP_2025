<?php

declare(strict_types=1);

namespace App\Console\IO;

final class ConsoleOutput
{
    public function writeln(string $message): string
    {
        return $message . PHP_EOL;
    }

    public function error(string $message): string
    {
        return $message . PHP_EOL;
    }
}
