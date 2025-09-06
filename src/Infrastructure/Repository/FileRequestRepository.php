<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Application\Interface\RequestRepositoryInterface;
use App\Domain\Entity\Request;
use App\Domain\ValueObject\Priority;
use App\Domain\ValueObject\RequestId;
use App\Domain\ValueObject\RequestStatus;
use App\Infrastructure\Config\AppConfig;
use App\Infrastructure\Logging\Logger;
use DateTimeImmutable;
use Exception;
use RuntimeException;

final class FileRequestRepository implements RequestRepositoryInterface
{
    private string $storagePath;
    private \Monolog\Logger $logger;

    public function __construct()
    {
        $this->storagePath = AppConfig::getStoragePath() . '/requests';
        $this->logger = Logger::getInstance();

        if (!is_dir($this->storagePath)) {
            if (!mkdir($concurrentDirectory = $this->storagePath, 0755, true) && !is_dir($concurrentDirectory)) {
                throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function save(Request $request): void
    {
        $filename = $this->getFilename($request->id()->value());

        $data = [
            'id' => $request->id()->value(),
            'data' => $request->data(),
            'priority' => $request->priority()->value(),
            'status' => $request->status()->value(),
            'createdAt' => $request->createdAt()->format('c'),
            'processedAt' => $request->processedAt()?->format('c'),
            'result' => $request->result(),
            'error' => $request->error(),
        ];

        $result = file_put_contents($filename, json_encode($data, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));

        if ($result === false) {
            throw new RuntimeException("Failed to save request to file: {$filename}");
        }

        $this->logger->info('Request saved to file', [
            'requestId' => $request->id()->value(),
            'filename' => $filename,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function findById(string $id): ?Request
    {
        $filename = $this->getFilename($id);

        if (!file_exists($filename)) {
            return null;
        }

        $content = file_get_contents($filename);
        if ($content === false) {
            return null;
        }

        $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        return $this->fromArray($data);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(): array
    {
        $requests = [];
        $files = glob($this->storagePath . '/*.json');

        foreach ($files as $file) {
            $content = file_get_contents($file);
            if ($content === false) {
                continue;
            }

            $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
            $request = $this->fromArray($data);
            if ($request !== null) {
                $requests[] = $request;
            }
        }

        return $requests;
    }

    /**
     * Формирует путь к файлу запроса
     */
    private function getFilename(string $id): string
    {
        return $this->storagePath . '/' . $id . '.json';
    }

    /**
     * Создает объект Request из массива данных
     */
    private function fromArray(array $data): ?Request
    {
        try {
            return new Request(
                new RequestId($data['id']),
                $data['data'],
                new Priority($data['priority']),
                new RequestStatus($data['status']),
                new DateTimeImmutable($data['createdAt']),
                $data['processedAt'] ? new DateTimeImmutable($data['processedAt']) : null,
                $data['result'] ?? null,
                $data['error'] ?? null
            );
        } catch (Exception $e) {
            $this->logger->error('Failed to create Request from array', [
                'data' => $data,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
