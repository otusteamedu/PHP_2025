<?php

declare(strict_types=1);

namespace App\Storage;

use App\Model\Book;

/**
 * Интерфейс хранилища книг.
 * Определяет контракт для работы с различными бэкендами (Elasticsearch, PostgreSQL и т.д.)
 */
interface BookStorageInterface
{
    /**
     * Пересоздать индекс (удалить старый и создать новый).
     */
    public function recreateIndex(): void;

    /**
     * Массовая индексация книг.
     *
     * @param Book[] $books Массив книг для индексации
     * @return int Количество успешно проиндексированных книг
     */
    public function bulkIndex(array $books): int;

    /**
     * Поиск книг.
     *
     * @param string|null $query Поисковый запрос (необязательно)
     * @param string|null $category Фильтр по категории (необязательно)
     * @param int|null $maxPrice Максимальная цена (необязательно)
     * @param bool $inStockOnly Только в наличии (по умолчанию false)
     * @return Book[] Найденные книги
     */
    public function search(
        ?string $query = null,
        ?string $category = null,
        ?int $maxPrice = null,
        bool $inStockOnly = false,
    ): array;
}