<?php

namespace App\Services;

use App\Repositories\ElasticsearchRepository;

class SearchService
{
    private ElasticsearchRepository $repository;
 
    public function __construct(ElasticsearchRepository $repository)
    {
        $this->repository = $repository;
    }

    public function initializeData(): void
    {
        $jsonFile = __DIR__ . '/../data/books.json';
        
        if (!file_exists($jsonFile)) {
            throw new \RuntimeException("Файл с данными не найден: " . $jsonFile);
        }
    
        // Читаем файл построчно
        $lines = file($jsonFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        if (empty($lines)) {
            throw new \RuntimeException('Файл с данными пуст');
        }
    
        $bulkData = [];
        $books = [];
    
        // Обрабатываем bulk формат
        for ($i = 0; $i < count($lines); $i += 2) {
            if (!isset($lines[$i + 1])) {
                break;
            }
    
            $createLine = json_decode($lines[$i], true);
            $bookData = json_decode($lines[$i + 1], true);
    
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \RuntimeException('Ошибка парсинга JSON: ' . json_last_error_msg());
            }
    
            $books[] = $this->transformBookData($bookData);
        }
    
        $this->repository->createIndex();
        $this->repository->indexBooks($books);
    }

    private function transformBookData(array $bookData): array{
    // Преобразуем структуру stock
    $totalStock = 0;
    foreach ($bookData['stock'] as $shopStock) {
        $totalStock += $shopStock['stock'];
    }

    return [
        'title' => $bookData['title'],
        'sku' => $bookData['sku'],
        'category' => $bookData['category'],
        'price' => $bookData['price'],
        'stock' => $totalStock, // Суммируем остатки по всем магазинам
        'stock_details' => $bookData['stock'] // Сохраняем детальную информацию
    ];
}

    public function search(array $params): array
    {
        $query = $params['query'] ?? '';
        $category = $params['category'] ?? null;
        $maxPrice = isset($params['max-price']) ? (float)$params['max-price'] : null;
        $inStock = isset($params['in-stock']);

        return $this->repository->searchBooks($query, $category, $maxPrice, $inStock);
    }
}