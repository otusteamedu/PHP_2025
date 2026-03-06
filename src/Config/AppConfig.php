<?php
declare(strict_types=1);

namespace App\Config;

use RuntimeException;

class AppConfig
{
    public function __construct(
        public readonly string $rabbitHost,
        public readonly int $rabbitPort,
        public readonly string $rabbitUser,
        public readonly string $rabbitPass,
        public readonly string $rabbitVhost,
        public readonly string $mailHost,
        public readonly int $mailPort,
        public readonly string $mailFromEmail,
        public readonly string $mailFromName,
    ) {}

    public static function fromEnvironment(): self
    {
        return new self(
            rabbitHost: self::getString('RABBITMQ_HOST'),
            rabbitPort: self::getInt('RABBITMQ_PORT'),
            rabbitUser: self::getString('RABBITMQ_DEFAULT_USER'),
            rabbitPass: self::getString('RABBITMQ_DEFAULT_PASS'),
            rabbitVhost: self::getString('RABBITMQ_VHOST'),
            mailHost: self::getString('MAILHOG_HOST'),
            mailPort: self::getInt('MAILHOG_PORT'),
            mailFromEmail: self::getString('MAILHOG_FROM_EMAIL'),
            mailFromName: self::getString('MAILHOG_FROM_NAME'),
        );
    }

    private static function getString(string $key): string
    {
        $value = $_ENV[$key] ?? null;

        if (!is_string($value) || $value === '') {
            throw new RuntimeException(sprintf('Environment variable "%s" is missing or empty.', $key));
        }

        return $value;
    }

    private static function getInt(string $key): int
    {
        $value = self::getString($key);

        $intValue = filter_var($value, FILTER_VALIDATE_INT);
        if ($intValue === false) {
            throw new RuntimeException(sprintf('Environment variable "%s" must be an integer.', $key));
        }

        return (int) $value;
    }
}
