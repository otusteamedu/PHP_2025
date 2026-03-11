<?php
declare(strict_types=1);

namespace Pryaniki\App\Presentation\Console;

use Elastic\Elasticsearch\ClientBuilder;
use \Pryaniki\App\App;
use Pryaniki\App\Infrastructure\Elasticsearch\ElasticsearchIndexManager;

class ConsoleSearchRunner
{

    public function run(): void
    {

        $app = new App();

        $argv = $_SERVER['argv'];

        $command = end($argv) ?? null;

        $client = ClientBuilder::create()
            ->setHosts([getenv('ELASTICSEARCH_HOST') ?: 'http://elasticsearch:9200'])
            ->build();

        $indexManager = new ElasticsearchIndexManager($client);

        switch ($command) {
            case 'import':
                $indexManager->createIndex();
                $file = $argv[1] ?? '';

                if ($file === '' || !file_exists($file)) {
                    echo "File not found\n";
                    exit(1);
                }

                $app->importFromFile($file);
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
                $app->search($args);
                break;
            default:
                echo "Unknown command: $command\n";
                exit(1);
        }
    }
}