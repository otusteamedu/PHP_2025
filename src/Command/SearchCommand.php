<?php

namespace App\Command;

use App\Search\SearchQueryBuilder;
use App\Search\SearchService;
use App\Service\ElasticsearchClient;
use LucidFrame\Console\ConsoleTable;

class SearchCommand
{
    public function execute(array $options): int
    {
        $query = $options['q'] ?? $options['query'] ?? null;
        $category = $options['c'] ?? $options['category'] ?? null;
        $maxPrice = $options['p'] ?? $options['price'] ?? null;
        $minStock = $options['m'] ?? $options['min-stock'] ?? 1;
        $indexName = $options['index-name'] ?? 'otus-shop';

        if (!$query) {
            $this->showHelp();
            return 1;
        }

        try {
            // Create search query using builder
            $queryBuilder = new SearchQueryBuilder($indexName);
            $queryBuilder->withSearchTerm($query);

            if ($category) {
                $queryBuilder->withCategory($category);
            }

            if ($maxPrice) {
                $queryBuilder->withMaxPrice((int)$maxPrice);
            }

            if ($minStock) {
                $queryBuilder->withMinStock((int)$minStock);
            }

            $searchParams = $queryBuilder->build();

            // Execute search
            $searchService = new SearchService(new ElasticsearchClient());
            $response = $searchService->search($searchParams);

            $this->displayResults($response);
            return 0;
        } catch (\Exception $e) {
            echo "Ошибка: " . $e->getMessage() . "\n";
            return 1;
        }
    }

    private function displayResults(array $response): void
    {
        $table = new ConsoleTable();

        $hits = $response['hits']['hits'] ?? [];
        $total = $response['hits']['total']['value'] ?? 0;

        if ($total === 0) {
            echo "Ничего не найдено\n";
            return;
        }

        // Set table headers
        $table->setHeaders(["Название", "Категория", "Цена", "В наличии"]);

        foreach ($hits as $hit) {
            $source = $hit['_source'];
            $totalStock = array_sum(array_column($source['stock'], 'stock'));

            $table->addRow([
                $source['title'],
                $source['category'],
                $source['price'],
                $totalStock
            ]);
        }

        $table
            ->addFooter('Найдено товаров:')
            ->addFooter($total, ConsoleTable::ALIGN_RIGHT)
            ->display();
    }

    private function showHelp(): void
    {
        echo "Использование: php console.php search [опции]\n";
        echo "Опции:\n";
        echo "  -q, --query=STRING     Поисковый запрос (обязательный)\n";
        echo "  -c, --category=STRING  Фильтр по категории\n";
        echo "  -p, --price=NUMBER     Максимальная цена\n";
        echo "  -m, --min-stock=NUMBER Минимальное количество на складе (по умолчанию 1)\n";
        echo "  --index-name=STRING    Имя индекса (по умолчанию otus-shop)\n";
    }
}