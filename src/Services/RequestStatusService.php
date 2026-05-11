<?php

declare(strict_types=1);

namespace App\Services;

use InvalidArgumentException;
use Predis\Client;
use RuntimeException;

final class RequestStatusService
{
    private Client $redis;
    private string $keyPrefix;
    private int $ttlSeconds;

    public function __construct()
    {
        $host = $this->envString('REDIS_HOST', 'redis');
        $port = $this->envInt('REDIS_PORT', 6379);
        $password = $this->envString('REDIS_PASSWORD', '');
        $database = $this->envInt('REDIS_DB', 0);
        $timeout = $this->envFloat('REDIS_TIMEOUT_SECONDS', 2.5);

        $this->keyPrefix = $this->envString('REQUEST_STATUS_KEY_PREFIX', 'request_status:');
        $this->ttlSeconds = $this->envInt('REQUEST_STATUS_TTL_SECONDS', 604800);

        $parameters = [
            'scheme' => 'tcp',
            'host' => $host,
            'port' => $port,
            'database' => $database,
            'timeout' => $timeout,
        ];

        if ($password !== '') {
            $parameters['password'] = $password;
        }

        $this->redis = new Client($parameters);

        try {
            $this->redis->connect();
            $this->redis->ping();
        } catch (\Throwable $e) {
            throw new RuntimeException('Redis connection error: ' . $e->getMessage(), 0, $e);
        }
    }

    public function create(string $requestId, array $payload): array
    {
        $now = gmdate('c');

        $record = [
            'request_id' => $requestId,
            'status' => 'queued',
            'payload' => $payload,
            'result' => null,
            'error' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        $this->writeRecord($requestId, $record);

        return $record;
    }

    public function get(string $requestId): ?array
    {
        $raw = $this->redis->get($this->buildKey($requestId));

        if ($raw === null) {
            return null;
        }

        return $this->decodeRecord((string) $raw);
    }

    public function markProcessing(string $requestId): array
    {
        return $this->update($requestId, function (array &$record): void {
            $record['status'] = 'processing';
            $record['started_at'] = gmdate('c');
        });
    }

    public function markCompleted(string $requestId, array $result): array
    {
        return $this->update($requestId, function (array &$record) use ($result): void {
            $record['status'] = 'completed';
            $record['result'] = $result;
            $record['error'] = null;
            $record['completed_at'] = gmdate('c');
        });
    }

    public function markFailed(string $requestId, string $error): array
    {
        return $this->update($requestId, function (array &$record) use ($error): void {
            $record['status'] = 'failed';
            $record['error'] = $error;
            $record['completed_at'] = gmdate('c');
        });
    }

    public function markQueuedForRetry(string $requestId, string $error): array
    {
        return $this->update($requestId, function (array &$record) use ($error): void {
            $record['status'] = 'queued';
            $record['error'] = $error;
            $record['retries'] = isset($record['retries']) ? ((int) $record['retries'] + 1) : 1;
            unset($record['started_at'], $record['completed_at']);
        });
    }

    private function update(string $requestId, callable $mutator): array
    {
        $key = $this->buildKey($requestId);
        $raw = $this->redis->get($key);

        if ($raw === null) {
            throw new RuntimeException('Request status not found.');
        }

        $record = $this->decodeRecord((string) $raw);
        $mutator($record);
        $record['updated_at'] = gmdate('c');

        $this->writeRecord($requestId, $record);

        return $record;
    }

    private function writeRecord(string $requestId, array $record): void
    {
        $encoded = json_encode($record, JSON_UNESCAPED_UNICODE);

        if ($encoded === false) {
            throw new RuntimeException('Failed to encode request status to JSON.');
        }

        $key = $this->buildKey($requestId);
        $written = $this->ttlSeconds > 0
            ? $this->redis->setex($key, $this->ttlSeconds, $encoded)
            : $this->redis->set($key, $encoded);

        if ((string) $written !== 'OK') {
            throw new RuntimeException('Failed to write request status to Redis.');
        }
    }

    private function decodeRecord(string $raw): array
    {
        $decoded = json_decode($raw, true);

        if (!is_array($decoded)) {
            throw new RuntimeException('Corrupted request status in Redis.');
        }

        return $decoded;
    }

    private function buildKey(string $requestId): string
    {
        if (!preg_match('/^[A-Za-z0-9_-]{8,128}$/', $requestId)) {
            throw new InvalidArgumentException('Invalid request id format.');
        }

        return $this->keyPrefix . $requestId;
    }

    private function envString(string $key, string $default): string
    {
        $value = getenv($key);

        if ($value === false || $value === '') {
            return $default;
        }

        return $value;
    }

    private function envInt(string $key, int $default): int
    {
        $value = getenv($key);

        if ($value === false || $value === '') {
            return $default;
        }

        if (!is_numeric($value)) {
            throw new InvalidArgumentException(sprintf('Environment variable %s must be numeric.', $key));
        }

        return (int) $value;
    }

    private function envFloat(string $key, float $default): float
    {
        $value = getenv($key);

        if ($value === false || $value === '') {
            return $default;
        }

        if (!is_numeric($value)) {
            throw new InvalidArgumentException(sprintf('Environment variable %s must be numeric.', $key));
        }

        return (float) $value;
    }
}
