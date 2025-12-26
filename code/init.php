<?php
require __DIR__ . '/vendor/autoload.php';
use Ak\Hw\Models\Elastic;

$settingsArray = [
    "analysis" => [
        "analyzer" => [
            'russian_custom' =>[
                "type" => "standard",
                "stopwords" => "_russian_",
                'filter' => ['lowercase','russian_stemmer','russian_morphology']
            ]
        ]
    ]
];
$mappingArray = [
    'properties' => [
        'title'    => ['type' => 'text'],
        'sku'      => ['type' => 'keyword'],
        'category' => ['type' => 'keyword'],
        'price'    => ['type' => 'float'],
        'stock'    => [
            'type'       => 'nested',
            'properties' => [
                'shop'  => ['type' => 'keyword'],
                'stock' => ['type' => 'integer'],
            ],
        ],
    ],
];

$el = new Elastic('otus-shop');
try {
    $el->init($settingsArray, $mappingArray);
    echo "Индекс создан.";
} catch (JsonException $e) {
    echo "Ошибка при создании индекса: ". $e->getMessage();
}


$jsonFilePath = __DIR__ . '/books.json';
if (!file_exists($jsonFilePath)) {
    die("Ошибка: файл {$jsonFilePath} не найден.");
}

try {
    $el->bulk($jsonFilePath);
    echo "Успешно загружено.";
} catch (Exception $e) {
    echo "Ошибка при загрузке данных: ". $e->getMessage();
}