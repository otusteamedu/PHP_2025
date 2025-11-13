<?php

declare(strict_types=1);

require_once __DIR__.'/vendor/autoload.php';

use App\Output\TableFormatter;
use App\Repository\ElasticsearchBookRepository;
use App\Service\SearchService;

function printUsage(): void
{
    echo "Использование:\n";
    echo "  php search.php --query=\"<текст>\" [опции]\n\n";
    echo "Опции:\n";
    echo "  --query=\"<текст>\"      Текст поиска (обязательно)\n";
    echo "  --category=\"<категория>\" Фильтр по категории\n";
    echo "  --max-price=<число>     Максимальная цена\n";
    echo "  --in-stock              Только в наличии\n\n";
    echo "Пример:\n";
    echo "  php search.php --query=\"рыцОри\" --category=\"Исторический роман\" --max-price=2000 --in-stock\n";
}

function showError(string $message): void
{
    fwrite(STDERR, "Ошибка: $message\n");
    exit(1);
}

try {
    $config = require __DIR__.'/config/elasticsearch.php';

    $repository = ElasticsearchBookRepository::create($config);
    $service = new SearchService($repository);
    $formatter = new TableFormatter;

    if (count($argv) < 2) {
        printUsage();
        exit(0);
    }

    $books = $service->search($argv);

    echo $formatter->format($books);

} catch (Throwable $e) {
    showError($e->getMessage());
}
