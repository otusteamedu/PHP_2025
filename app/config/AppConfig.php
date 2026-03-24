<?php

declare(strict_types=1);

namespace App\Config;

final class AppConfig
{
    public function __construct(
        public readonly string $timezone = 'Asia/Aqtau',
        public readonly string $dbHost = '127.0.0.1',
        public readonly string $dbName = 'skyd_db',
        public readonly string $dbUser = 'root',
        public readonly string $dbPass = '',
        
        // Настройки бизнеса
        public readonly string $lunchStart = '12:00:00',
        public readonly string $lunchEnd   = '13:00:00',
        public readonly string $workStart  = '08:00:00',
        public readonly string $workEnd    = '17:00:00',
        public readonly int    $graceMinutes = 1,
        public readonly int    $timePrecisionMinutes = 2,
    ) {}

    public static function fromEnv(): self
    {
        return new self(
            timezone: $_ENV['TIMEZONE'] ?? 'Asia/Aqtau',
            dbHost: $_ENV['DB_HOST'] ?? '127.0.0.1',
            dbName: $_ENV['DB_NAME'] ?? 'skyd_db',
            // и т.д.
        );
    }
}