<?php

namespace Otus\Code\Application;

use Otus\Code\Infrastructure\Elasticsearch\ElasticSearchService;
use Otus\Code\Application\SearchResultFormatter;
use Exception;

class App {

    const MAP = [
            'title' => ['class' => '\Otus\Code\Infrastructure\Elasticsearch\Filters\MatchFilter', 'operator' => 'must'],
            'category' => ['class' => '\Otus\Code\Infrastructure\Elasticsearch\Filters\TermFilter', 'operator' => 'filter'],
            'price' => ['class' => '\Otus\Code\Infrastructure\Elasticsearch\Filters\RangeFilter', 'operator' => 'filter'],
            'inStock' => ['class' => '\Otus\Code\Infrastructure\Elasticsearch\Filters\NestedFilter', 'operator' => 'filter', 'field' => 'stock']
        ];

    private $search_options;

    public function __construct()
    {
        $this->search_options = getopt("", [ 
            "title:",
            "category:",
            "price:",
            "inStock:",
            "help:"
        ]);
    }

    public function run() {
         if (isset($this->search_options['help'])) {
            $this->showHelp();
            exit(0);
        }

        try {
            $esService = new ElasticSearchService();
            $esService->createIndex($_ENV['ELASTIC_INDEX']);
            $esService->downloadDocuments($_ENV['ELASTIC_INDEX'], $_ENV['ELASTIC_FILEBASE']);
            $results = $esService->search($_ENV['ELASTIC_INDEX'], $this->search_options, self::MAP);

            SearchResultFormatter::displayResults($results['hits']);
        } catch (Exception $e) {
            echo "Ошибка: " . $e->getMessage() . "\n";
        }
    }

    private function showHelp(): void
    {
        echo "\nИспользование: php console.php [ПАРАМЕТРЫ]\n\n";
        echo "Параметры поиска:\n";
        echo "  --title 'текст'        Поиск по названию книги\n";
        echo "  --category 'категория' Фильтр по категории\n";
        echo "  --price N              Максимальная цена\n";
        echo "  --inStock [0|1]       1 - только в наличии, 0 - все\n";
        echo "  --help                 Показать эту справку\n\n";
        echo "Примеры:\n";
        echo "  php console.php --title 'рыцарь'\n";
        echo "  php console.php --category 'Фантастика' --price 1000\n";
        echo "  php console.php --title 'война' --inStock 1\n";
        echo "\n";
    }
}