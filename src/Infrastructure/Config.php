<?php

declare(strict_types=1);

namespace App\Infrastructure;

use InvalidArgumentException;

final class Config
{
    /** @var array<string,string> */
    private array $values = [];

    public function __construct(string $envPath)
    {
        if (!is_file($envPath)) {
            throw new InvalidArgumentException(".env file not found: {$envPath}");
        }
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            $parts = explode('=', $line, 2);
            if (count($parts) === 2) {
                $key = trim($parts[0]);
                $val = trim($parts[1]);
                $this->values[$key] = $val;
            }
        }
    }

    public function get(string $key, ?string $default = null): ?string
    {
        return $this->values[$key] ?? $default;
    }
}
