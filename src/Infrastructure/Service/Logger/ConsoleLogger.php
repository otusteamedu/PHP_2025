<?php
declare(strict_types=1);

namespace Dinargab\Homework19\Infrastructure\Service\Logger;

use Dinargab\Homework19\Domain\Console\ConsoleLoggerInterface;

class ConsoleLogger implements ConsoleLoggerInterface
{

    public function log(string $message): void
    {
        echo "\033[32m" . $message . "\033[0m" . PHP_EOL;;
    }
}