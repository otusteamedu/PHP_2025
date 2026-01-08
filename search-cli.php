#!/usr/bin/php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use Arlex2305k\BooksShop\Db\Elasticsearch;
use Arlex2305k\BooksShop\Service\Search;
use Arlex2305k\BooksShop\Util\TableOutput;

$shortOptions = "q:c:p:s";
$longOptions = [
	"query:",
	"category:",
	"price:",
	"in-stock"
];

$options = getopt($shortOptions, $longOptions);

$query = $options['query'] ?? $options['q'] ?? '';
$category = $options['category'] ?? $options['c'] ?? '';
$maxPrice = $options['price'] ?? $options['p'] ?? null;
$inStock = isset($options['in-stock']) || isset($options['s']);

if ($maxPrice !== null) {
	if (!is_numeric($maxPrice)) {
		echo "Ошибка: Цена должна быть числом.\n";
		exit(1);
	}
	$maxPrice = (float)$maxPrice;
}

$repository = new Elasticsearch();
$searchService = new Search($repository);

$results = [];

$searchOptions = [];
if (!empty($category)) {
	$searchOptions['category'] = $category;
}
if ($maxPrice !== null) {
	$searchOptions['max_price'] = $maxPrice;
}
if ($inStock) {
	$searchOptions['in_stock'] = true;
}

$results = $searchService->search($query, $searchOptions);

TableOutput::displayBooks($results);
