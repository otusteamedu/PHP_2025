<?php
declare(strict_types=1);


namespace App\Infrastructure\Repository;

use App\App;
use App\Domain\Mapper\BookMapper;
use App\Domain\Query\QueryBuilder;
use App\Domain\Query\Type;
use App\Domain\Repository\BookFilter;
use App\Domain\Repository\BookRepositoryInterface;
use App\Domain\Repository\PaginationResult;
use App\Infrastructure\Service\ClientBuilder;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;

class BookRepository implements BookRepositoryInterface
{
    private Client $client;
    private QueryBuilder $queryBuilder;
    private string $db = 'otus-shop';
    private BookMapper $bookMapper;

    /**
     * @throws AuthenticationException
     */
    public function __construct()
    {
        $this->client = ClientBuilder::build(
            App::$app->getConfigValue('elasticHost'),
            App::$app->getConfigValue('elasticPort'),
            App::$app->getConfigValue('elasticUsername'),
            App::$app->getConfigValue('elasticPassword'));
        $this->queryBuilder = new QueryBuilder();
        $this->bookMapper = new BookMapper();
    }

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @throws MissingParameterException
     */
    public function dbCreate(string $dbTitle, ?array $mappings = null, ?array $settings = null): bool
    {
        $data = [
            'index' => $dbTitle,
            'body' => [
                'settings' => App::$app->getConfigValue('indexSettings'),
                'mappings' => App::$app->getConfigValue('indexMappings')
            ]
        ];
        if ($mappings) {
            $data['body']['mappings'] = $mappings;
        }
        if ($settings) {
            $data['body']['settings'] = $settings;
        }

        return $this->client->index($data)->asBool();
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     * @throws MissingParameterException
     */
    public function dbDelete(string $dbTitle): bool
    {
        $data = [
            'index' => $dbTitle,
        ];

        return $this->client->indices()->delete($data)->asBool();
    }

    public function bulkInsert(string $itemsData, ?string $dbTitle = null): array
    {
        return $this->client->bulk([
            'index' => $dbTitle ?? $this->db,
            'body' => $itemsData,
        ])->asArray();
    }

    public function search(BookFilter $filter, ?string $dbTitle = null): PaginationResult
    {
        if ($filter->getTitle()) {
            $this->queryBuilder->addMust(Type::MATCH, 'title', $filter->getTitle());
        }
        if ($filter->getCategory()) {
            $this->queryBuilder->addMust(Type::TERM, 'category', $filter->getCategory());
        }
        if (!$filter->getRange()->isEmpty()) {
            $data = [];
            if ($filter->getRange()->min) {
                $data['gte'] = $filter->getRange()->min;
            }
            if ($filter->getRange()->max) {
                $data['lte'] = $filter->getRange()->max;
            }
            $this->queryBuilder->addMust(Type::RANGE, 'price', $data);
        }
        if ($filter->getIsInStock()) {
            $this->queryBuilder->addShould(Type::RANGE, 'stock.stock', ['gt' => 0]);
        }
        $this->queryBuilder->addLimit($filter->getPager()->getLimit());
        $this->queryBuilder->addPage($filter->getPager()->getOffset());

        $result = $this->client->search([
            'index' => $dbTitle ?? $this->db,
            'body' => $this->queryBuilder->getQuery()->jsonSerialize(),
        ])->asArray();
        $total = $result['hits']['total']['value'] ?? null;
        $items = [];
        if ($total) {
            foreach ($result['hits']['hits'] as $book) {
                $items[] = $this->bookMapper->mapEntity($book);
            }
        }

        return new PaginationResult($items, $total);
    }
}