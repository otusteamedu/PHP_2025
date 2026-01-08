<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Elastic\Elasticsearch\ClientBuilder;
use App\Repository\BookRepository;


try {
    $client = ClientBuilder::create()
        ->setHosts(['elasticsearch:9200'])
        ->build();

    $client->info();
} catch (\Exception $e) {
    die("Критическая ошибка: Не удалось подключиться к Elasticsearch. " . $e->getMessage() . "\n");
}


$jsonPath = __DIR__ . '/../db/books.json';

if (!file_exists($jsonPath)) {
    die("Ошибка: Файл данных не найден по пути: $jsonPath\n");
}


echo "Начинаю чтение файла: $jsonPath ...\n";

// Читаем файл в массив строк, пропуская пустые строки
$lines = file($jsonPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

if ($lines === false || empty($lines)) {
    die("Ошибка: Файл пуст или недоступен для чтения.\n");
}

echo "Всего строк обнаружено: " . count($lines) . "\n";
echo "Начинаю импорт порциями...\n";

$batchSize = 250;
$currentBatch = [];
$totalImported = 0;

foreach ($lines as $index => $line) {
    $currentBatch[] = $line;

    if (count($currentBatch) >= ($batchSize * 2)) {
        processBulkRequest($client, $currentBatch);
        $totalImported += ($batchSize);
        echo "Загружено документов: $totalImported ...\n";
        $currentBatch = [];
    }
}

// Отправляем остаток, если он есть
if (!empty($currentBatch)) {
    // Проверка на корректность структуры (должно быть четное количество строк)
    if (count($currentBatch) % 2 !== 0) {
        echo "Внимание: В последней порции нечетное количество строк. Возможна ошибка в JSON.\n";
    }
    processBulkRequest($client, $currentBatch);
    echo "Импорт завершен.\n";
} else {
    echo "Импорт завершен успешно!\n";
}

/**
 * Функция для отправки Bulk запроса
 * * @param \Elastic\Elasticsearch\Client $client
 * @param array $batch
 */
function processBulkRequest($client, array $batch) {
    try {
        // Формируем строку: каждая часть разделена \n, в конце тоже \n
        $body = implode("\n", $batch) . "\n";
        
        $response = $client->bulk([
            'body' => $body
        ]);

        // Если в Bulk запросе были ошибки для отдельных документов
        if (isset($response['errors']) && $response['errors'] === true) {
            echo "Внимание: Некоторые документы в пачке не были импортированы.\n";
        }
    } catch (\Exception $e) {
        echo "Ошибка Bulk-запроса: " . $e->getMessage() . "\n";
        // Если ошибка критическая (например, Malformed metadata), выводим детали
        if (strpos($e->getMessage(), 'Malformed') !== false) {
            echo "Совет: Проверьте структуру NDJSON в файле books.json.\n";
        }
    }
}