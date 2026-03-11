<?php
declare(strict_types=1);

namespace Pryaniki\App\Presentation\Console;

use Elastic\Elasticsearch\ClientBuilder;
use Pryaniki\App\Infrastructure\Elasticsearch\ElasticsearchIndexManager;
use Pryaniki\App\Infrastructure\Elasticsearch\ElasticsearchProductRepository;
use Pryaniki\App\Presentation\Controllers\Commands\Elasticsearch\ImportAction;
use Pryaniki\App\Presentation\Controllers\Commands\Elasticsearch\SearchAction;
use Pryaniki\App\Presentation\Views\TableView;

class ConsoleSearchRunner
{

    public function run(): void
    {
        $argv = $_SERVER['argv'];

        $command = end($argv) ?? null;

        $client = ClientBuilder::create()
            ->setHosts([getenv('ELASTICSEARCH_HOST') ?: 'http://elasticsearch:9200'])
            ->build();

        $indexManager = new ElasticsearchIndexManager($client);
        $productRepository = new ElasticsearchProductRepository($client);

        switch ($command) {
            case 'import':
                $indexManager->createIndex();
                $file = $argv[1] ?? '';

                if ($file === '' || !file_exists($file)) {
                    echo "File not found\n";
                    exit(1);
                }
                $action = new ImportAction($client);
                try {
                    $action->import($file);

                } catch (\RuntimeException $e) {
                    echo $e->getMessage() . "\n";
                    exit(1);
                }
                echo "Import completed\n";
                break;
            case 'create-index':
                $indexManager->createIndex();
                echo "Index has been created\n";
                break;
            case 'reset-index':
                $indexManager->resetIndex();
                echo "Index has been reset\n";
                break;
            case 'search':
                $query = $argv[1] ?? '';
                if ($query === '') {
                    echo "Search query is required\n";
                    exit(1);
                }

                $args = getopt('', [
                    'query::',
                    'category::',
                    'price::',
                    'price-from::',
                    'price-to::',
                    'stock::',
                    'stock-from::',
                    'stock-to::',
                    'shop::'
                ]);

                $action = new SearchAction($productRepository);
                $searchData = $action->run($args);
                $tableConfig = [
                    'title' => [
                        'name' => 'Название',
                        'width' => 60
                    ],
                    'category' => [
                        'name' => 'Категория',
                        'width' => 25
                    ],
                    'price' => [
                        'name' => 'Цена',
                        'width' => 8
                    ]
                ];
                $view = new TableView($tableConfig);
                $view->printTable($searchData);
                break;
            default:
                echo "Unknown command: $command\n";
                exit(1);
        }
    }
}