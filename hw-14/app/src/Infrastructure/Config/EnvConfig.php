<?php

declare(strict_types=1);

namespace App\Infrastructure\Config;

use App\Domain\Interfaces\ConfigInterface;

class EnvConfig implements ConfigInterface
{
    private array $config = [];

    public function __construct(?string $envFile = null)
    {
        if($envFile === null) {
            $envFile = dirname(__DIR__, 3) . '/.env';
        }

        $this->loadEnvFile($envFile);
    }

    public function get(string $key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return isset($this->config[$key]);
    }

    private function loadEnvFile(string $envFile): void
    {
        if (!file_exists($envFile)) {
            return;
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                $value = $this->unquoteValue($value);

                if (!array_key_exists($key, $this->config)) {
                    $this->config[$key] = $value;
                }
            }
        }
    }

    private function unquoteValue(string $value): string
    {
        if (preg_match('/^"(.+)"$/', $value, $matches)) {
            return $matches[1];
        }
        if (preg_match("/^'(.+)'$/", $value, $matches)) {
            return $matches[1];
        }
        return $value;
    }
}
