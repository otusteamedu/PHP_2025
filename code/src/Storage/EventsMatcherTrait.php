<?php

declare(strict_types=1);

namespace App\Storage;

trait EventsMatcherTrait
{
    protected function filterAndSort(array $events, array $params): array
    {
        $matches = [];

        foreach ($events as $event) {
            if (!is_array($event)) {
                continue;
            }

            if (!$this->matchesConditions($event, $params)) {
                continue;
            }

            $event['priority'] = (int)($event['priority'] ?? 0);
            $matches[] = $event;
        }

        usort($matches, static function (array $a, array $b): int {
            return ($b['priority'] ?? 0) <=> ($a['priority'] ?? 0);
        });

        return $matches;
    }

    protected function matchesConditions(array $event, array $params): bool
    {
        if (!isset($event['conditions']) || !is_array($event['conditions'])) {
            return false;
        }

        foreach ($event['conditions'] as $key => $value) {
            if (!array_key_exists($key, $params)) {
                return false;
            }

            if ($params[$key] !== $value) {
                return false;
            }
        }

        return true;
    }
}
