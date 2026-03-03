<?php

use \Pryaniki\App\App;

require __DIR__ . '/../vendor/autoload.php';

$app = new App();
$app->createIndex();

$argv = $_SERVER['argv'];

$command = end($argv) ?? null;

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

if ($command === '') {
    echo "Usage:\n";
    echo "  php app.php <query> search\n";
    echo "  php app.php <file> import\n";
    exit(1);
}

switch ($command) {
    case 'import':
        $file = $argv[1] ?? '';

        if ($file === '' || !file_exists($file)) {
            echo "File not found\n";
            exit(1);
        }

        $app->importFromFile($file);
        break;
    case 'reset-index':
        $app->resetIndex();
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