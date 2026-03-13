<?php

namespace Infrastructure\Model;

use DateTimeInterface;

class Counter {

    public const NOTIFICATION_TEXTS = [];

    public static function getCounterCheckDateAndInterval(int $deviceSerialnumber, string $token): array {
        // код метода
        return [[],[]];
    }
        
    public static function getNextCheckDate(string $lastCheckDate, int $checkInterval): ?DateTimeInterface {
        // код метода
        return null;
    }

    public static function getCheckDeadlineStatus(?DateTimeInterface $nextCheckDate): string {
        // код метода
        return '';
    }
}