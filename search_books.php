#!/usr/bin/env php
<?php

require 'vendor/autoload.php';

use Elastic\Elasticsearch\ClientBuilder;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableStyle;
use Symfony\Component\Console\Output\ConsoleOutput;

$client = ClientBuilder::create()->setHosts(['http://elasticsearch:9200'])->build();

// Читаем параметры командной строки
$params = getopt('', ['query:', 'category:', 'max_price:', 'in_stock::']);

$query = $params['query'] ?? '';
$category = $params['category'] ?? null;
$maxPrice = isset($params['max_price']) ? (float)$params['max_price'] : null;
$inStock = isset($params['in_stock']) ? (int)$params['in_stock'] : null;

// Формирование поискового запроса
$searchQuery = [
	"bool" => [
		"must" => [
			[
				"match" => [
					"title" => [
						"query" => $query,
						"fuzziness" => "AUTO"
					]
				]
			]
		],
		"filter" => []
	]
];

if ($category) {
	$searchQuery['bool']['filter'][] = ["term" => ["category.keyword" => $category]];
}
if ($maxPrice !== null) {
	$searchQuery['bool']['filter'][] = ["range" => ["price" => ["lte" => $maxPrice]]];
}
if ($inStock !== null) {
	$searchQuery['bool']['filter'][] = [
		"nested" => [
			"path" => "stock",
			"query" => [
				"bool" => [
					"filter" => [
						["range" => ["stock.stock" => ["gt" => 0]]]
					]
				]
			]
		]
	];
}

$response = $client->search([
	'index' => 'otus-shop',
	'body' => ['query' => $searchQuery]
]);

$books = $response['hits']['hits'] ?? [];

$output = new ConsoleOutput();
$table = new Table($output);
$table->setHeaders(['Название', 'Категория', 'Цена', 'Остаток']);

$tableStyle = new TableStyle();
$tableStyle->setPadType(STR_PAD_BOTH);
$table->setStyle($tableStyle);

foreach ($books as $book) {
	$source = $book['_source'];

	$stockData = array_map(fn($s) => "{$s['shop']}: {$s['stock']} шт.", $source['stock']);
	$stockString = implode("\n", $stockData); // Разделяем переносами строк

	$table->addRow([
		$source['title'],
		$source['category'],
		number_format($source['price'], 2, '.', ' ') . ' ₽',
		$stockString
	]);
}

if (empty($books)) {
	$output->writeln("<comment>Ничего не найдено.</comment>");
} else {
	$table->render();
}
