<?php

declare(strict_types=1);

namespace Ak\Hw\Application\Factory;

class CookingEventFactory
{
    public static function createEvent(string $type): CookingEventInterface
    {
        return match ($type) {
            'pre' => new PreCookingEvent(),
            'post' => new PostCookingEvent(),
            default => throw new \InvalidArgumentException("Invalid event type"),
        };
    }
}
