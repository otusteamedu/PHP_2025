<?php

declare(strict_types=1);

namespace App\Service;

use App\Infrastructure\Repository\BookshopRepository;
use App\Model\BookshopSearchModel;

class BookshopService
{
    private readonly BookshopRepository $bookshopRepository;

    public function __construct()
    {
        $this->bookshopRepository = new BookshopRepository();
    }

    public function createIndex(string $indexName, array $params = []): bool
    {
        return $this->bookshopRepository->createIndex($indexName, $params);
    }

    public function deleteIndex(string $indexName): bool
    {
        return $this->bookshopRepository->deleteIndex($indexName);
    }

    public function existsIndex(string $indexName): bool
    {
        return $this->bookshopRepository->existsIndex($indexName);
    }

    public function loadBulkDocuments(array $data): bool
    {
        return $this->bookshopRepository->loadBulkDocuments($data);
    }

    public function searchDocuments(string $indexName, BookshopSearchModel $bookshopSearchModel): array
    {
        return $this->bookshopRepository->searchDocuments($indexName, $bookshopSearchModel);
    }
}
