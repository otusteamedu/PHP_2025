<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Elastic\Elasticsearch\ClientBuilder;
use App\Repository\BookRepository;


$client = ClientBuilder::create()
    ->setHosts(['elasticsearch:9200']) 
    ->build();

$repository = new BookRepository($client);

$queryText = $argv[1] ?? '';
$maxPrice = isset($argv[2]) ? (float)$argv[2] : null;

if (empty($queryText)) {
    die("Использование: php search.php \"[название]\" [максимальная цена]\n");
}

try {
    $results = $repository->search($queryText, $maxPrice);
} catch (\Exception $e) {
    die("Ошибка поиска: " . $e->getMessage() . "\n");
}

if (empty($results['hits']['hits'])) {
    echo "\nНичего не найдено по запросу: \"$queryText\"\n";
    exit;
}

echo "\nРезультаты поиска:\n";
echo str_repeat("-", 100) . "\n";
printf("%-45s | %-20s | %-10s | %-10s\n", 'Название', 'Категория', 'Цена', 'Score');
echo str_repeat("-", 100) . "\n";

foreach ($results['hits']['hits'] as $hit) {
    $source = $hit['_source'];
    
    $title = mb_strimwidth($source['title'], 0, 43, "...");
    $category = mb_strimwidth($source['category'], 0, 18, "...");
    $price = $source['price'] . ' тенге.';
    $score = round($hit['_score'], 2);

    printf("%-45s | %-20s | %-10s | %-10s\n", 
        $title, 
        $category, 
        $price, 
        $score
    );
}
echo str_repeat("-", 100) . "\n";
echo "Всего найдено: " . $results['hits']['total']['value'] . "\n\n";