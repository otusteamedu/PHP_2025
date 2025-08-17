<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Domain\Models\SearchCriteria;

interface BookRepositoryInterface
{
    /**
     * Поиск книг по критериям
     */
    public function search(SearchCriteria $criteria): array;

    /**
     * Массовая индексация книг
     */
    public function bulkIndex(array $books): void;

    /**
     * Создание индекса
     */
    public function createIndex(): void;

    /**
     * Проверка существования индекса
     */
    public function indexExists(): bool;

    /**
     * Удаление индекса
     */
    public function deleteIndex(): void;
}
