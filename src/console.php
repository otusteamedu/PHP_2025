<?php declare(strict_types=1);

require 'vendor/autoload.php';

use App\Command\InitCommand;
use App\Command\SearchCommand;

// Парсим аргументы командной строки
$options = getopt('q:c:p:m:h', ['search', 'init', 'query:', 'category:', 'price:', 'min-stock:', 'help', 'index-name:']);

try {
    if (isset($options['init'])) {
        $initCommand = new InitCommand();
        exit($initCommand->execute($options));
    } else if (isset($options['search'])) {
        if (isset($options['h']) || isset($options['help'])) {
            showSearchHelp();
            exit(0);
        }

        $searchCommand = new SearchCommand();
        exit($searchCommand->execute($options));
    } else {
        showHelp();
        exit(1);
    }
} catch (\Exception $e) {
    echo "Ошибка: " . $e->getMessage() . "\n";
    exit(1);
}

function showHelp(): void
{
    echo "Использование: php src/console.php [команда] [опции]\n\n";
    echo "Доступные команды:\n";
    echo "  --init                  Инициализировать индекс Elasticsearch\n";
    echo "  --search                Поиск товаров\n";
    echo "\n";
    echo "Для получения помощи по конкретной команде, используйте:\n";
    echo "  php src/console.php --search --help\n";
}

function showSearchHelp(): void
{
    echo "Использование: php src/console.php --search [опции]\n";
    echo "Опции:\n";
    echo "  -q, --query=STRING     Поисковый запрос (обязательный)\n";
    echo "  -c, --category=STRING  Фильтр по категории\n";
    echo "  -p, --price=NUMBER     Максимальная цена\n";
    echo "  -s, --stock=NUMBER     Минимальное количество на складе (по умолчанию 1)\n";
    echo "  --index-name=STRING    Имя индекса (по умолчанию otus-shop)\n";
    echo "  -h, --help             Показать эту справку\n";
}