<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Domain\Models\Book;
use App\Domain\Repositories\BookRepositoryInterface;
use InvalidArgumentException;
use RuntimeException;

final readonly class IndexService
{
    public function __construct(
        private BookRepositoryInterface $bookRepository
    ) {
    }

    /**
     * Создает индекс Elasticsearch, удаляя существующий если он есть
     */
    public function createIndex(): void
    {
        if ($this->bookRepository->indexExists()) {
            $this->bookRepository->deleteIndex();
        }
        
        $this->bookRepository->createIndex();
    }

    /**
     * Индексирует книги из JSON файла
     */
    public function indexBooksFromFile(string $filePath): int
    {
        if (!file_exists($filePath)) {
            throw new InvalidArgumentException("Файл не найден: {$filePath}");
        }

        $books = $this->parseBooksFile($filePath);
        $this->bookRepository->bulkIndex($books);

        return count($books);
    }

    /**
     * Парсит JSON файл с книгами
     */
    private function parseBooksFile(string $filePath): array
    {
        $books = [];
        $handle = fopen($filePath, 'r');
        
        if (!$handle) {
            throw new RuntimeException("Не удается открыть файл: {$filePath}");
        }

        while (($line = fgets($handle)) !== false) {
            $line = trim($line);
            
            if (empty($line)) {
                continue;
            }

            $data = json_decode($line, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                continue;
            }

            if (isset($data['create'])) {
                continue;
            }

            if (isset($data['title'])) {
                $books[] = Book::fromArray($data);
            }
        }

        fclose($handle);
        return $books;
    }
}