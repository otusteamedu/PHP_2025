<?php
require 'vendor/autoload.php';

use Elasticsearch\ClientBuilder;

$client = ClientBuilder::create()->setHosts(['localhost:9200'])->build();
$file = 'books.json';

if (!file_exists($file)) {
    echo "Файл не найден.\n";
    exit;
}

$bulkData = [];
$lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach ($lines as $line) {
    $json = json_decode($line, true);
    if ($json === null) {
        echo "Ошибка в строке JSON: $line\n";
        continue;
    }

    $bulkData[] = $json;
}

if (!empty($bulkData)) {
    $params = ['body' => $bulkData];
    try {
        $response = $client->bulk($params);
        if (isset($response['errors']) && $response['errors'] == true) {
            echo "Ошибка при загрузке данных.\n";
        } else {
            echo "Данные успешно загружены.\n";
        }
    } catch (Exception $e) {
        echo "Ошибка Elasticsearch: " . $e->getMessage() . "\n";
    }
}
