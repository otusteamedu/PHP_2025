<?php

namespace App\Console;

use Elastic\Elasticsearch\ClientBuilder;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableStyle;
use Symfony\Component\Console\Output\ConsoleOutput;

class SearchBooksCommand
{
	private $client;

	public function __construct()
	{
		$this->client = ClientBuilder::create()->setHosts(['http://elasticsearch:9200'])->build();
	}

	public function run($args)
	{
		$options = $this->parseArgs($args);

		// Формирование поискового запроса
		$searchQuery = [
			"bool" => [
				"must" => [
					[
						"match" => [
							"title" => [
								"query" => $options['query'],
								"fuzziness" => "AUTO"
							]
						]
					]
				],
				"filter" => []
			]
		];

		if ($options['category']) {
			$searchQuery['bool']['filter'][] = ["term" => ["category.keyword" => $options['category']]];
		}
		if ($options['max_price'] !== null) {
			$searchQuery['bool']['filter'][] = ["range" => ["price" => ["lte" => $options['max_price']]]];
		}
		if ($options['in_stock'] !== null) {
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

		$response = $this->client->search([
			'index' => 'otus-shop',
			'body' => ['query' => $searchQuery]
		]);

		$this->printResults($response);
	}

	private function printResults($response)
	{
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
	}

	private function parseArgs($args)
	{
		$options = [
			'query'     => '',
			'category'  => '',
			'max_price' => PHP_INT_MAX,
			'in_stock'  => null
		];

		foreach ($args as $arg) {
			if (preg_match('/--query=(.+)/', $arg, $match)) {
				$options['query'] = $match[1];
			}
			if (preg_match('/--category=(.+)/', $arg, $match)) {
				$options['category'] = $match[1];
			}
			if (preg_match('/--max_price=(\d+)/', $arg, $match)) {
				$options['max_price'] = (int) $match[1];
			}
			if (preg_match('/--in_stock=(\d+)/', $arg, $match)) {
				$options['in_stock'] = (int) $match[1];
			}
		}

		return $options;
	}
}