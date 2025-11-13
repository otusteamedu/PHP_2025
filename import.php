<?php

declare(strict_types=1);

require_once __DIR__.'/vendor/autoload.php';

use Elastic\Elasticsearch\ClientBuilder;

/**
 * Скрипт импорта книг из books.json в Elasticsearch
 */
const INDEX_NAME = 'otus-shop';
const BOOKS_FILE = __DIR__.'/books.json';

echo "Загрузка конфигурации...\n";
$config = require __DIR__.'/config/elasticsearch.php';

echo "Подключение к Elasticsearch...\n";
$client = ClientBuilder::create()
    ->setHosts([$config['host'].':'.$config['port']])
    ->build();

// Проверяем доступность Elasticsearch
try {
    $client->ping();
    echo "✓ Подключено к Elasticsearch\n";
} catch (Exception $e) {
    echo '✗ Ошибка подключения к Elasticsearch: '.$e->getMessage()."\n";
    exit(1);
}

// Удаляем индекс, если существует
try {
    if ($client->indices()->exists(['index' => INDEX_NAME])) {
        echo "Удаление существующего индекса...\n";
        $client->indices()->delete(['index' => INDEX_NAME]);
        echo "✓ Индекс удален\n";
    }
} catch (Exception $e) {
    // Игнорируем ошибки при удалении, возможно индекс не существует
}

echo "Создание индекса с маппингом...\n";
$client->indices()->create([
    'index' => INDEX_NAME,
    'body' => [
        'mappings' => [
            'properties' => [
                'title' => [
                    'type' => 'text',
                    'fields' => [
                        'keyword' => [
                            'type' => 'keyword',
                        ],
                        'ru' => [
                            'type' => 'text',
                            'analyzer' => 'russian',
                        ],
                    ],
                ],
                'category' => [
                    'type' => 'text',
                    'fields' => [
                        'keyword' => [
                            'type' => 'keyword',
                        ],
                    ],
                ],
                'price' => [
                    'type' => 'integer',
                ],
                'stock' => [
                    'type' => 'nested',
                    'properties' => [
                        'shop' => [
                            'type' => 'keyword',
                        ],
                        'stock' => [
                            'type' => 'integer',
                        ],
                    ],
                ],
            ],
        ],
    ],
]);
echo "✓ Индекс создан\n";

// Проверяем наличие файла с данными
if (! file_exists(BOOKS_FILE)) {
    echo '✗ Файл '.BOOKS_FILE." не найден\n";
    exit(1);
}

echo "Импорт через curl...\n";

$scheme = $_ENV['ELASTICSEARCH_SCHEME'] ?? 'http';
$host = $config['host'].':'.$config['port'];

$auth = '';
if (! empty($_ENV['ELASTIC_USER']) && ! empty($_ENV['ELASTIC_PASS'])) {
    $auth = ' -u '.escapeshellarg($_ENV['ELASTIC_USER'].':'.$_ENV['ELASTIC_PASS']);
}

$insecure = ! empty($_ENV['ELASTIC_INSECURE']) ? ' --insecure' : '';

$cmd = sprintf(
    'curl --location%s%s --silent --show-error --fail --request POST %s://%s/_bulk?refresh=true '
    .'--header %s --data-binary %s',
    $insecure,
    $auth,
    escapeshellarg($scheme),
    escapeshellarg($host),
    escapeshellarg('Content-Type: application/x-ndjson'),
    escapeshellarg('@'.BOOKS_FILE)
);

exec($cmd.' 2>&1', $out, $code);
if ($code !== 0) {
    fwrite(STDERR, "✗ Ошибка bulk-импорта через curl:\n".implode("\n", $out)."\n");
    exit(1);
}

echo "✓ Импорт через curl завершён\n";

echo "Ожидание обновления индекса...\n";
$client->indices()->refresh(['index' => INDEX_NAME]);

$stats = $client->count(['index' => INDEX_NAME]);
$actualCount = $stats['count'];

echo "✓ Индекс обновлен, документов в индексе: $actualCount\n";
echo "✓ Импорт завершен успешно!\n";
