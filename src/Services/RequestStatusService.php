<?php

declare(strict_types=1);

namespace App\Services;

use InvalidArgumentException;
use RuntimeException;

final class RequestStatusService
{
    private string $storagePath;

    public function __construct(?string $storagePath = null)
    {
        $this->storagePath = $storagePath ?? __DIR__ . '/../storage/requests';

        if (!is_dir($this->storagePath) && !mkdir($this->storagePath, 0775, true) && !is_dir($this->storagePath)) {
            throw new RuntimeException('Unable to create storage directory for request statuses.');
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
        $path = $this->buildPath($requestId);

        if (!is_file($path)) {
            return null;
        }

        return $this->readRecord($path);
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

    private function update(string $requestId, callable $mutator): array
    {
        $path = $this->buildPath($requestId);

        if (!is_file($path)) {
            throw new RuntimeException('Request status not found.');
        }

        $record = $this->readRecord($path);
        $mutator($record);
        $record['updated_at'] = gmdate('c');

        $this->writeRecord($requestId, $record);

        return $record;
    }

    private function writeRecord(string $requestId, array $record): void
    {
        $path = $this->buildPath($requestId);
        $encoded = json_encode($record, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        if ($encoded === false) {
            throw new RuntimeException('Failed to encode request status to JSON.');
        }

        $bytes = file_put_contents($path, $encoded . PHP_EOL, LOCK_EX);

        if ($bytes === false) {
            throw new RuntimeException('Failed to write request status file.');
        }
    }

    private function readRecord(string $path): array
    {
        $content = file_get_contents($path);

        if ($content === false) {
            throw new RuntimeException('Failed to read request status file.');
        }

        $decoded = json_decode($content, true);

        if (!is_array($decoded)) {
            throw new RuntimeException('Corrupted request status file.');
        }

        return $decoded;
    }

    private function buildPath(string $requestId): string
    {
        if (!preg_match('/^[A-Za-z0-9_-]{8,128}$/', $requestId)) {
            throw new InvalidArgumentException('Invalid request id format.');
        }

        return $this->storagePath . DIRECTORY_SEPARATOR . $requestId . '.json';
    }
}
