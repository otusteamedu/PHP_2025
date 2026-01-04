<?php

declare(strict_types=1);

namespace Otus\Elasticsearch\Command;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Otus\Elasticsearch\ES\Operators\MustOperator;
use Otus\Elasticsearch\ES\Queries\DummyQuery;
use Otus\Elasticsearch\ES\Queries\FuzzinessQuery;
use Otus\Elasticsearch\ES\Queries\RangeQuery;
use Otus\Elasticsearch\ES\Queries\TermQuery;
use Otus\Elasticsearch\Factory\ESCFactory;

readonly class SearchCommand
{
    /**
     * @var Client
     */
    protected Client $client;

    /**
     * @throws AuthenticationException
     */
    public function __construct()
    {
        $this->client = ESCFactory::factory();
    }

    /**
     * @param array $args
     *
     * @return int
     *
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function __invoke(string ...$args): int
    {
        $queries = $this->getQueries($args);
        $must = new MustOperator($queries);

        $response = $this
            ->client
            ->search([
                'index' => 'otus-shop',
                'body' => [
                    'query' => [
                        'bool' => $must->toArray(),
                    ],
                ],
            ]);

        [
            'hits' => [
                'hits' => $hits,
            ],
        ] = json_decode($response->getBody()->getContents(), true);

        $this->render($hits);

        return 0;
    }

    /**
     * @param array $hits
     */
    protected function render(array $hits): void
    {
        echo implode(PHP_EOL . '---' . PHP_EOL, array_map(static function (array $hit): string {
            return PHP_EOL . implode(PHP_EOL, [
                'ID : ' . $hit['_id'],
                'Title : ' . $hit['_source']['title'],
                'SKU : ' . $hit['_source']['sku'],
                'Category : ' . $hit['_source']['category'],
                'Price : ' . $hit['_source']['price'],
                'Stock : ' . PHP_EOL . implode(PHP_EOL, array_map(static function (array $entity): string {
                    [
                        'shop' => $shop,
                        'stock' => $stock,
                    ] = $entity;

                    return $shop . ' : ' . $stock;
                }, $hit['_source']['stock'])),
            ]) . PHP_EOL;
        }, $hits));
    }

    /**
     * @param array $args
     *
     * @return array
     */
    protected function getQueries(array $args): array
    {
        $query = [];

        $opts = $this->getOpts($args);

        foreach ($opts as $field => $value) {
            $query[] = match ($field) {
                'title', 'category' => new FuzzinessQuery($field, $value),
                'sku' => new TermQuery($field, $value),
                'price' => new RangeQuery($field, 'gte', $value),

                default => new DummyQuery(),
            };
        }

        return $query;
    }

    /**
     * @param array $args
     *
     * @return array
     */
    protected function getOpts(array $args): array
    {
        $opts = [];

        foreach ($args as $arg) {
            if (str_starts_with($arg, '--') && str_contains($arg, '=')) {
                [
                    $field,
                    $value,
                ] = explode('=', substr($arg, 2), 2);

                $opts[$field] = $value;
            }
        }

        return $opts;
    }
}
