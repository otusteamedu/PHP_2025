<?php

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/vendor/autoload.php';

use App\Elasticsearch\ElasticsearchClient;
use App\Services\BookService;
use App\Controllers\BookController;

$client = new ElasticsearchClient(ELASTICSEARCH_URL, ELASTICSEARCH_USER, ELASTICSEARCH_PASS);
$bookService = new BookService($client);
$bookController = new BookController($bookService);

function showMenu() {
    echo "Выберите действие:\n";
    echo "1. Создать индекс\n";
    echo "2. Загрузить данные\n";
    echo "3. Поиск книг\n";
    echo "4. Выйти\n";
}

while (true) {
    showMenu();
    echo "Введите номер действия: ";
    $action = trim(fgets(STDIN));

    switch ($action) {
        case '1':
            echo $bookController->createIndex(__DIR__ . '/data/otus_shop_index.json');
            break;
        case '2':
            echo $bookController->bulkInsert(__DIR__ . '/data/otus_shop_content.json');
            break;
        case '3':
            $bookController->searchBooks();
            break;
        case '4':
            echo "Выход из приложения.\n";
            exit(0);
        default:
            echo "Неверный выбор. Попробуйте снова.\n";
            break;
    }
}