<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Repositories\ElasticsearchRepository;
use App\Services\SearchService;
use App\Applications\ConsoleApplication;

// Проверяем, запущен ли скрипт из командной строки
if (php_sapi_name() !== 'cli') {
    echo "Это приложение должно запускаться только из командной строки\n";
    exit(1);
}

try {
    $repository = new ElasticsearchRepository();
    $searchService = new SearchService($repository);
    $app = new ConsoleApplication($searchService);

    $app->run($argv);
} catch (Exception $e) {
    echo "Ошибка: " . $e->getMessage() . "\n";
    exit(1);
}