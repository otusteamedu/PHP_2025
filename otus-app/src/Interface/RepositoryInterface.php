<?php

declare(strict_types=1);

namespace App\Interface;

interface RepositoryInterface
{
    public function addBulk(array $bookList, ?string $indexName = null): array;
    public function searchByQuery(array $query, ?string $indexName = null): array;
    public function create(array $settings, ?string $indexName = null): void;
    public function delete(?string $indexName = null): void;
}
