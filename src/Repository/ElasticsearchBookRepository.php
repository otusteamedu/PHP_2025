<?php

declare(strict_types=1);

namespace App\Repository;

use App\DTO\Book;
use App\DTO\BookSearchParams;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use stdClass;

/**
 * Репозиторий для работы с книгами в Elasticsearch
 */
class ElasticsearchBookRepository implements BookRepositoryInterface
{
    public function __construct(
        private Client $client,
        private string $index
    ) {}

    /**
     * Создает репозиторий с настройками из конфига
     *
     * @param  array<string,string>  $config
     */
    public static function create(array $config): self
    {
        $client = ClientBuilder::create()
            ->setHosts([$config['host'].':'.$config['port']])
            ->build();

        return new self($client, $config['index']);
    }

    public function search(BookSearchParams $params): array
    {
        $query = $this->buildQuery($params);

        $response = $this->client->search([
            'index' => $this->index,
            'body' => [
                'query' => $query,
                'size' => 100,
            ],
        ]);

        return $this->mapResults($response['hits']['hits']);
    }

    /**
     * Формирует Elasticsearch запрос из параметров поиска
     */
    private function buildQuery(BookSearchParams $params): array
    {
        $must = [];
        $filter = [];

        // Поиск по тексту с опечатками и морфологией
        if ($params->query !== null && $params->query !== '') {
            $must[] = [
                'multi_match' => [
                    'query' => $params->query,
                    'fields' => ['title^2', 'title.ru'],
                    'fuzziness' => 'AUTO',
                    'operator' => 'and',
                ],
            ];
        }

        // Фильтр по категории
        if ($params->category !== null) {
            $filter[] = [
                'term' => [
                    'category.keyword' => $params->category,
                ],
            ];
        }

        // Фильтр по максимальной цене
        if ($params->maxPrice !== null) {
            $filter[] = [
                'range' => [
                    'price' => [
                        'lte' => $params->maxPrice,
                    ],
                ],
            ];
        }

        // Фильтр по наличию на складе
        if ($params->inStock) {
            $filter[] = [
                'nested' => [
                    'path' => 'stock',
                    'query' => [
                        'range' => [
                            'stock.stock' => [
                                'gt' => 0,
                            ],
                        ],
                    ],
                ],
            ];
        }

        // Если нет условий поиска, возвращаем match_all
        if (empty($must) && empty($filter)) {
            return ['match_all' => new stdClass];
        }

        $bool = [];
        if (! empty($must)) {
            $bool['must'] = $must;
        }
        if (! empty($filter)) {
            $bool['filter'] = $filter;
        }

        return ['bool' => $bool];
    }

    /**
     * Преобразует результаты Elasticsearch в массив Book
     *
     * @param  array<int,mixed>  $hits
     * @return Book[]
     */
    private function mapResults(array $hits): array
    {
        return array_map(
            fn (array $hit) => Book::fromArray($hit['_source']),
            $hits
        );
    }
}
