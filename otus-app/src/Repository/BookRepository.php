<?php

declare(strict_types=1);

namespace App\Repository;

use App\Interface\RepositoryInterface;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;

class BookRepository extends BaseRepository implements RepositoryInterface
{
    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function addBulk(array $bookList, ?string $indexName = null): array
    {
        return $this->client->bulk(
            [
                'body' => $bookList,
                'index' => $indexName ?? $this->defaultIndex,
            ],
        )->asArray();
    }

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function searchByQuery(array $query, ?string $indexName = null): array
    {
        return $this->client->search(
            [
                'body' => $query,
                'index' => $indexName ?? $this->defaultIndex,
            ],
        )->asArray();
    }

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @throws MissingParameterException
     */
    public function create(array $settings, ?string $indexName = null): void
    {
        $this->client->indices()->create(
            [
                'index' => $indexName ?? $this->defaultIndex,
                'body' => $settings,
            ],
        )->asArray();
    }

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @throws MissingParameterException
     */
    public function delete(?string $indexName = null): void
    {
        $this->client->indices()->delete(
            [
                'index' => $indexName ?? $this->defaultIndex,
            ],
        )->asArray();
    }
}
