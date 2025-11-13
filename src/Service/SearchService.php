<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\Book;
use App\DTO\BookSearchParams;
use App\DTO\SearchParamsInterface;
use App\Repository\BookRepositoryInterface;

/**
 * Сервис поиска книг
 */
class SearchService
{
    public function __construct(
        private BookRepositoryInterface $repository
    ) {}

    /**
     * Выполняет поиск книг по параметрам командной строки
     *
     * @param  array<string>  $argv
     * @return Book[]
     */
    public function search(array $argv): array
    {
        $params = $this->parseParams($argv);

        return $this->repository->search($params);
    }

    /**
     * Парсит аргументы командной строки
     *
     * @param  array<string>  $argv
     */
    private function parseParams(array $argv): SearchParamsInterface
    {
        $query = null;
        $category = null;
        $maxPrice = null;
        $inStock = false;

        foreach ($argv as $arg) {
            if (str_starts_with($arg, '--query=')) {
                $query = $this->extractValue($arg);
            } elseif (str_starts_with($arg, '--category=')) {
                $category = $this->extractValue($arg);
            } elseif (str_starts_with($arg, '--max-price=')) {
                $maxPrice = (int) $this->extractValue($arg);
            } elseif ($arg === '--in-stock') {
                $inStock = true;
            }
        }

        return new BookSearchParams(
            query: $query,
            category: $category,
            maxPrice: $maxPrice,
            inStock: $inStock
        );
    }

    /**
     * Извлекает значение из параметра вида --key=value
     */
    private function extractValue(string $arg): string
    {
        $parts = explode('=', $arg, 2);

        return $parts[1] ?? '';
    }
}
