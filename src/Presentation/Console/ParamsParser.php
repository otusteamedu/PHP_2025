<?php

declare(strict_types=1);

namespace App\Presentation\Console;

final class ParamsParser
{
    public function parse(string $input): array
    {
        $result = [];

        $pairs = explode(',', $input);

        foreach ($pairs as $pair) {

            $pair = trim($pair);

            if ($pair === '') {
                continue;
            }

            $parts = preg_split('/\s+/', $pair);

            if (count($parts) !== 2) {
                continue;
            }

            [$key, $value] = $parts;

            $result[$key] = is_numeric($value)
                ? (int)$value
                : $value;
        }

        return $result;
    }
}