<?php

require 'vendor/autoload.php';

use App\ElasticClient;
use App\Indexer;
use App\DataLoader;
use App\Searcher;

$elastic = new ElasticClient();
$indexer = new Indexer($elastic);
$dataLoader = new DataLoader($elastic);
$searcher = new Searcher($elastic);

$command = $argv[1] ?? '';

switch ($command) {
    case 'create-index':
        $indexer->createIndex('otus-shop');
        break;

    case 'load-data':
        $dataLoader->loadData(__DIR__ . '\books.json', 'otus-shop');
        break;

    case 'search':
        $query = $argv[2] ?? '';
        $maxPrice = isset($argv[3]) ? (float)$argv[3] : null;
        $searcher->search($query, $maxPrice);
        break;

    default:
        echo "Команды:\n";
        echo "  create-index   - создать индекс\n";
        echo "  load-data      - загрузить данные из books.json\n";
        echo "  search <word> [maxPrice] - выполнить поиск\n";
}
