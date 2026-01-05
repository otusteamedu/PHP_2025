<?php

require 'vendor/autoload.php';

use Elastic\Elasticsearch\ClientBuilder;
use App\Repository\BookRepository;
use LucadeMarchi\ConsoleTable\Table;

$client = ClientBuilder::create()->setHosts(['localhost:9200'])->build();
$repository = new BookRepository($client);

// Пример запуска: php search.php "рыцОри" 2000
$queryText = $argv[1] ?? '';
$maxPrice = isset($argv[2]) ? (float)$argv[2] : null;

if (empty($queryText)) {
    die("Использование: php search.php [название] [максимальная цена]\n");
}

$results = $repository->search($queryText, $maxPrice);

$table = new Table();
$table->addHeader('Название')
      ->addHeader('Категория')
      ->addHeader('Цена')
      ->addHeader('Склад')
      ->addHeader('Score (Релевантность)');

foreach ($results['hits']['hits'] as $hit) {
    $source = $hit['_source'];
    $table->addRow([
        $source['title'],
        $source['category'],
        $source['price'] . ' руб.',
        $source['stock'],
        $hit['_score']
    ]);
}

echo $table->display();