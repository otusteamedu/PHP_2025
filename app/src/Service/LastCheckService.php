<?php
declare(strict_types=1);

namespace App\Service;

use DateTimeImmutable;

final readonly class LastCheckService
{
    private const string SESSION_LAST_CHECK_KEY = 'last_check';

    public function setCurrentTime(): void
    {
        $_SESSION[self::SESSION_LAST_CHECK_KEY] = time();
    }

    public function get(): string
    {
        $lastCheckTimestamp = $_SESSION[self::SESSION_LAST_CHECK_KEY] ?? null;

        if ($lastCheckTimestamp) {
            return DateTimeImmutable::createFromTimestamp($lastCheckTimestamp)->format('d.m.Y H:i:s');
        }

        return 'Не производилась';
    }
}
