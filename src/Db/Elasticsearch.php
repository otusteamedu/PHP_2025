<?php

namespace Arlex2305k\BooksShop\Db;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Arlex2305k\BooksShop\Entity\Book;
use Dotenv\Dotenv;

class Elasticsearch
{
	private Client $client;
	private string $indexName = 'otus-shop';

	public function __construct(string $host = 'localhost', int $port = 9200, string $username = 'elastic', string $password = null)
	{
		if ($password === null) {
			$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
			$dotenv->load();
			$password = $_ENV['ELASTIC_PASSWORD'] ?? getenv('ELASTIC_PASSWORD') ?: '';
		}

		$this->client = ClientBuilder::create()
			->setHosts(["{$host}:{$port}"])
			->setBasicAuthentication($username, $password)
			->build();
	}

	public function createIndex(): bool
	{
		$params = [
			'index' => $this->indexName,
			'body' => $this->getMapping()
		];

		try {
			$response = $this->client->indices()->create($params);
			return true;
		} catch (\Exception $e) {
			echo "Ошибка создания индекса: " . $e->getMessage() . PHP_EOL;
			return false;
		}
	}

	public function getMapping(): array
	{
		return [
			'settings' => [
				"analysis" => [
					"analyzer" => [
						"my_russian" => [
							"type" => "custom",
							"tokenizer" => "standard",
							"filter" => ["lowercase", "russian_stop", "russian_stemmer"]
						]
					],
					"filter" => [
						"russian_stop" => ["type" => "stop", "stopwords" => "_russian_"],
						"russian_stemmer" => ["type" => "stemmer", "language" => "russian"]
					]
				]],
			'mappings' => [
				'properties' => [
					'sku' => [
						'type' => 'keyword'
					],
					'title' => [
						'type' => 'text',
						'analyzer' => 'my_russian',
						'search_analyzer' => 'my_russian',
						'fields' => [
							'suggest' => [
								'type' => 'completion',
								'analyzer' => 'my_russian'
							]
						]
					],
					'category' => [
						'type' => 'text',
						'analyzer' => 'my_russian',
						'search_analyzer' => 'my_russian',
						'fields' => [
							'keyword' => [
								'type' => 'keyword'
							]
						]
					],
					'price' => [
						'type' => 'float'
					],
					'stock' => [
						'type' => 'nested',
						'properties' => [
							'shop' => [
								'type' => 'keyword'
							],
							'stock' => [
								'type' => 'integer'
							]
						]
					]
				]
			]
		];
	}

	public function bulkImportFromFile(string $filePath): bool
	{
		$file = fopen($filePath, 'r');
		if (!$file) {
			throw new \Exception("Не могу открыть файл: {$filePath}");
		}
		$bulkBody = [];
		$lineNum = 0;

		while (($line = fgets($file)) !== false) {
			$lineNum++;
			$data = json_decode($line, true);

			if ($lineNum % 2 === 1) {
				$bulkBody[] = $data;
			} else {
				$bulkBody[] = $data;
				if (count($bulkBody) >= 2000) {
					$this->executeBulk($bulkBody);
					$bulkBody = [];
				}
			}
		}

		if (!empty($bulkBody)) {
			$this->executeBulk($bulkBody);
		}
		fclose($file);
		return true;
	}

	private function executeBulk(array $bulkBody): void
	{
		$params = [
			'body' => $bulkBody
		];

		try {
			$responses = $this->client->bulk($params);

			if ($responses['errors']) {
				foreach ($responses['items'] as $item) {
					if (isset($item['index']['error'])) {
						echo "Ошибка импорта документа: " . $item['index']['error']['reason'] . PHP_EOL;
					}
				}
			}
		} catch (\Exception $e) {
			echo "Ошибка пакетного импорта: " . $e->getMessage() . PHP_EOL;
		}
	}

	public function searchBooks(string $query = '', array $filters = []): array
	{
		$searchParams = [
			'index' => $this->indexName,
			'body' => [
				'query' => [
					'bool' => [
						'must' => [],
						'filter' => []
					]
				],
				'sort' => [
					'_score' => ['order' => 'desc']
				]
			]
		];

		if (!empty($query)) {
			$searchParams['body']['query']['bool']['must'][] = [
				'multi_match' => [
					'query' => $query,
					'fields' => ['title^2', 'category'],
					'type' => 'best_fields',
					'fuzziness' => 'AUTO',
					'prefix_length' => 1
				]
			];
		} else {
			$searchParams['body']['query']['bool']['must'][] = [
				'match_all' => (object)[]
			];
		}

		if (isset($filters['category'])) {
			$searchParams['body']['query']['bool']['filter'][] = [
				'term' => ['category.keyword' => $filters['category']]
			];
		}

		if (isset($filters['max_price'])) {
			$searchParams['body']['query']['bool']['filter'][] = [
				'range' => [
					'price' => ['lte' => $filters['max_price']]
				]
			];
		}

		if (isset($filters['in_stock']) && $filters['in_stock']) {
			$searchParams['body']['query']['bool']['filter'][] = [
				'nested' => [
					'path' => 'stock',
					'query' => [
						'range' => [
							'stock.stock' => ['gt' => 0]
						]
					]
				]
			];
		}

		try {
			$results = $this->client->search($searchParams);

			$books = [];
			foreach ($results['hits']['hits'] as $hit) {
				$source = $hit['_source'];

				$book = new Book(
					$source['sku'],
					$source['title'],
					$source['category'],
					$source['price'],
					$source['stock']
				);

				$books[] = $book;
			}

			return $books;
		} catch (\Exception $e) {
			echo "Ошибка поиска: " . $e->getMessage() . PHP_EOL;
			return [];
		}
	}

	public function client(): Client
	{
		return $this->client;
	}
}
