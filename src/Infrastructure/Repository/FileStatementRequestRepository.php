<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Application\Interface\StatementRequestRepositoryInterface;
use App\Domain\Entity\BankStatementRequest;
use App\Domain\ValueObject\DateRange;
use App\Domain\ValueObject\TelegramChatId;
use App\Infrastructure\Logging\Logger;
use DateTimeImmutable;
use RuntimeException;

final class FileStatementRequestRepository implements StatementRequestRepositoryInterface
{
    public function __construct(
        private string $storageFile = '/var/www/html/storage/requests.json'
    ) {
        $this->ensureStorageDirectory();
    }

    /**
     * {@inheritdoc}
     */
    public function save(BankStatementRequest $request): void
    {
        $logger = Logger::getInstance();
        $logger->info('Saving statement request', ['id' => $request->id()]);

        $this->ensureStorageDirectory();

        $requests = $this->loadRequests();
        $requestData = $this->entityToArray($request);
        $requests[$request->id()] = $requestData;

        $result = file_put_contents(
            $this->storageFile,
            json_encode($requests, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        if ($result === false) {
            $logger->error('Failed to write to storage file', ['file' => $this->storageFile]);
            throw new RuntimeException('Failed to save statement request');
        }

        $logger->info('Successfully saved request to file', ['bytes_written' => $result]);
    }

    /**
     * {@inheritdoc}
     */
    public function findById(string $id): ?BankStatementRequest
    {
        $requests = $this->loadRequests();
        $data = $requests[$id] ?? null;

        if (!$data) {
            return null;
        }

        return $this->arrayToEntity($data);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(): array
    {
        $requests = $this->loadRequests();
        $entities = [];

        foreach ($requests as $data) {
            $entities[] = $this->arrayToEntity($data);
        }

        return $entities;
    }

    /**
     * {@inheritdoc}
     */
    public function findPending(): array
    {
        $requests = $this->loadRequests();
        $entities = [];

        foreach ($requests as $data) {
            if ($data['status'] === 'pending') {
                $entities[] = $this->arrayToEntity($data);
            }
        }

        return $entities;
    }

    /**
     * Загружает все запросы из файла requests.json
     */
    private function loadRequests(): array
    {
        if (!file_exists($this->storageFile)) {
            return [];
        }

        $content = file_get_contents($this->storageFile);
        if (!$content) {
            return [];
        }

        $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        return is_array($data) ? $data : [];
    }

    /**
     * Преобразует сущность в массив
     */
    private function entityToArray(BankStatementRequest $request): array
    {
        return [
            'id' => $request->id(),
            'startDate' => $request->dateRange()->startDate()->format('Y-m-d'),
            'endDate' => $request->dateRange()->endDate()->format('Y-m-d'),
            'telegramChatId' => $request->telegramChatId()->value(),
            'status' => $request->status(),
            'createdAt' => $request->createdAt()->format('Y-m-d H:i:s'),
            'processedAt' => $request->processedAt()?->format('Y-m-d H:i:s'),
            'result' => $request->result(),
        ];
    }

    /**
     * Преобразует массив в сущность
     */
    private function arrayToEntity(array $data): BankStatementRequest
    {
        $createdAt = new DateTimeImmutable($data['createdAt']);
        $processedAt = $data['processedAt'] ? new DateTimeImmutable($data['processedAt']) : null;

        return new BankStatementRequest(
            id: $data['id'],
            dateRange: new DateRange(
                new DateTimeImmutable($data['startDate']),
                new DateTimeImmutable($data['endDate'])
            ),
            telegramChatId: new TelegramChatId($data['telegramChatId']),
            createdAt: $createdAt,
            status: $data['status'],
            processedAt: $processedAt,
            result: $data['result'] ?? null
        );
    }

    /**
     * Обеспечивает существование директории для файла requests.json
     */
    private function ensureStorageDirectory(): void
    {
        $directory = dirname($this->storageFile);

        if (!is_dir($directory)) {
            if (!mkdir($directory, 0755, true) && !is_dir($directory)) {
                throw new RuntimeException('Failed to create storage directory: ' . $directory);
            }
        }
    }
}
