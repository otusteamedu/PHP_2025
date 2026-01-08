#!/usr/bin/php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use Arlex2305k\BooksShop\Db\Elasticsearch;

$repository = new Elasticsearch();

echo "Создание индекса...\n";
if ($repository->createIndex()) {
    echo "Индекс создан успешно.\n";
} else {
    echo "Ошибка создания индекса.\n";
    exit(1);
}

echo "Импорт из books.json...\n";
try {
    $repository->bulkImportFromFile(__DIR__ . '/books.json');
    echo "Импорт прошел успешно.\n";
} catch (\Exception $e) {
    echo "Ошибка импорта: " . $e->getMessage() . "\n";
    exit(1);
}
