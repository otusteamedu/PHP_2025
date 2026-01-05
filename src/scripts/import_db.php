<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Elastic\Elasticsearch\ClientBuilder;
use App\Repository\BookRepository;

// создадим клиента еластика
$client = ClientBuilder::create()
    ->setHosts(['localhost:9200'])
    ->build();

// получим экземпляр нашего репозитория
$repository = new BookRepository($client);

//  путь к данным
$jsonPath = __DIR__ . '/../db/books.json';

if (!file_exists($jsonPath)) {
    die("Ошибка: Файл $jsonPath не найден.\n");
}

$jsonData = file_get_contents($jsonPath);
$books = json_decode($jsonData, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die("Ошибка при разборе JSON: " . json_last_error_msg() . "\n");
}

echo "Начинаю импорт " . count($books) . " книг...\n";

// разобьем наши данные на чанки
$chunks = array_chunk($books, 100);

foreach ($chunks as $index => $chunk) {
    try {
        $repository->bulkIndex($chunk);
        echo "Порция " . ($index + 1) . " загружена.\n";
    } catch (\Exception $e) {
        echo "Ошибка в порции " . ($index + 1) . ": " . $e->getMessage() . "\n";
    }
}

echo "Импорт завершен успешно!\n";