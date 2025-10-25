<?php

declare(strict_types=1);

namespace App\Infrastructure\Logging;

use App\Infrastructure\Config\AppConfig;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger as MonologLogger;

final class Logger
{
    private static ?MonologLogger $instance = null;

    /**
     * Возвращает синглтон-экземпляр логгера
     */
    public static function getInstance(): MonologLogger
    {
        if (self::$instance === null) {
            self::$instance = new MonologLogger('app');

            $handler = new StreamHandler(
                AppConfig::getLogFile(),
                self::getLogLevel()
            );

            self::$instance->pushHandler($handler);
        }

        return self::$instance;
    }

    /**
     * Преобразует строковый уровень в объект Level
     */
    private static function getLogLevel(): Level
    {
        $level = AppConfig::getLogLevel();

        return match (strtolower($level)) {
            'debug' => Level::Debug,
            'info' => Level::Info,
            'notice' => Level::Notice,
            'warning' => Level::Warning,
            'error' => Level::Error,
            'critical' => Level::Critical,
            'alert' => Level::Alert,
            'emergency' => Level::Emergency,
            default => Level::Info,
        };
    }
}
