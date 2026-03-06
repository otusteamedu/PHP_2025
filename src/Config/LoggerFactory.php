<?php
declare(strict_types=1);

namespace App\Config;

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class LoggerFactory
{
    public static function create(string $name, bool $isCli): LoggerInterface
    {
        $logger = new Logger($name);

        $stream = $isCli ? 'php://stdout' : 'php://stderr';
        $logger->pushHandler(new StreamHandler($stream, Level::Info));

        return $logger;
    }
}
