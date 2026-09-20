<?php

declare(strict_types=1);

namespace App\Storage;

use InvalidArgumentException;

final class ConditionKey
{
    /** @param array<string, scalar> $conditions */
    public static function fromConditions(array $conditions): string
    {
        $parts = [];

        foreach ($conditions as $name => $value) {
            if (!is_string($name) || $name === '' || !is_scalar($value)) {
                throw new InvalidArgumentException('Параметры должны состоять из непустых строк и скалярных значений.');
            }

            $stringValue = is_bool($value) ? ($value ? 'true' : 'false') : (string) $value;
            $parts[rawurlencode($name)] = rawurlencode($stringValue);
        }

        ksort($parts, SORT_STRING);

        if ($parts === []) {
            return 'events:conditions:all';
        }

        $conditions = [];
        foreach ($parts as $name => $value) {
            $conditions[] = $name . '=' . $value;
        }

        return 'events:conditions:' . implode('|', $conditions);
    }

    /** @param array<string, scalar> $params @return list<array<string, scalar>> */
    public static function allSubsets(array $params): array
    {
        $subsets = [[]];

        foreach ($params as $name => $value) {
            $existingSubsets = $subsets;
            foreach ($existingSubsets as $subset) {
                $subset[$name] = $value;
                $subsets[] = $subset;
            }
        }

        return $subsets;
    }
}
