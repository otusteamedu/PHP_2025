<?php

namespace App\Applications;

use App\Services\SearchService;

class ConsoleApplication
{
    private SearchService $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    public function run(array $argv): void
    {
        $params = $this->parseArguments($argv);

        if (isset($params['help'])) {
            $this->showHelp();
            return;
        }

        if (isset($params['init'])) {
            $this->searchService->initializeData();
            return;
        }

        $results = $this->searchService->search($params);
        $this->displayResults($results);
    }

    private function parseArguments(array $argv): array
    {
        $params = [];
        
        for ($i = 1; $i < count($argv); $i++) {
            if (strpos($argv[$i], '--') === 0) {
                $key = substr($argv[$i], 2);
                
                if (isset($argv[$i + 1]) && strpos($argv[$i + 1], '--') !== 0) {
                    $params[$key] = $argv[$i + 1];
                    $i++;
                } else {
                    $params[$key] = true;
                }
            }
        }

        return $params;
    }

    private function showHelp(): void
    {
        echo "Использование:\n";
        echo "php index.php --init - инициализировать индекс и данные\n";
        echo "php index.php --query \"поисковый запрос\" [опции]\n";
        echo "\nОпции:\n";
        echo "--category CATEGORY - фильтр по категории\n";
        echo "--max-price PRICE - максимальная цена\n";
        echo "--in-stock - только товары в наличии\n";
        echo "--help - показать эту справку\n";
        echo "\nПример:\n";
        echo "php index.php --query \"рыцОри\" --category \"Исторический роман\" --max-price 2000 --in-stock\n";
    }

    private function displayResults(array $results): void
    {
        if (empty($results['hits']['hits'])) {
            echo "Ничего не найдено.\n";
            return;
        }

        echo sprintf(
            "%-40s %-25s %-10s %-8s %-10s\n",
            'Название',
            'Категория',
            'Цена',
            'В наличии',
            'Релевантность'
        );
        echo str_repeat('-', 95) . "\n";

        foreach ($results['hits']['hits'] as $hit) {
            $source = $hit['_source'];
            $score = round($hit['_score'], 2);
            
            echo sprintf(
                "%-40s %-25s %-10.2f %-8d %-10.2f\n",
                mb_substr($source['title'], 0, 37) . (mb_strlen($source['title']) > 37 ? '...' : ''),
                mb_substr($source['category'], 0, 22) . (mb_strlen($source['category']) > 22 ? '...' : ''),
                $source['price'],
                $source['stock'],
                $score
            );
        }

        echo "\nНайдено: " . $results['hits']['total']['value'] . " товаров\n";
    }
}