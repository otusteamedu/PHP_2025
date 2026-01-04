<?php

declare(strict_types=1);

namespace Otus\Elasticsearch\Command;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Otus\Elasticsearch\ES\Operators\MustOperator;
use Otus\Elasticsearch\ES\Queries\DummyQuery;
use Otus\Elasticsearch\ES\Queries\MatchQuery;
use Otus\Elasticsearch\ES\Queries\RangeQuery;
use Otus\Elasticsearch\ES\Queries\TermQuery;

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
        $this->client = ClientBuilder::create()
            ->setHosts([
                'https://localhost:9200',
            ])
            ->setSSLVerification(false)
            ->setBasicAuthentication('elastic', 'elastic')
            ->build();
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

        // Не стал заморачиваться над выводом
        print_r(json_decode($response->getBody()->getContents(), true)['hits']['hits']);

        return 0;
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
                'title', 'category' => new MatchQuery($field, $value),
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
