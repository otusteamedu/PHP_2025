<?php declare(strict_types=1);

namespace App\Elastic\Repositories;

use App\Application;
use App\Elastic\ClientBuilder;
use App\Elastic\Config;
use App\Elastic\Queries\BookQuery;
use App\Forms\Search\BookSearch;
use App\Models\Book;
use App\Repositories\ShopSearchRepositoryInterface;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;

/**
 * Class ShopSearchRepository
 * @package App\Elastic\Repositories
 */
class ShopSearchRepository implements ShopSearchRepositoryInterface
{
    /**
     * @var Config
     */
    private Config $config;
    /**
     * @var Client
     */
    private Client $client;

    /**
     * @throws AuthenticationException
     */
    public function __construct()
    {
        $this->config = new Config(Application::$app->getConfig());
        $this->client = ClientBuilder::create($this->config);
    }

    /**
     * @inheritdoc
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function search(BookSearch $bookSearch): array
    {
        $result = $this->client->search([
            'index' => $this->config->getStorageName(),
            'body' => BookQuery::create($bookSearch),
        ])->asArray();

        $booksHits = $result['hits']['hits'] ?? [];
        $books = [];

        foreach ($booksHits as $bookHit) {
            $books[] = new Book($bookHit['_source']);
        }

        return $books;
    }

    /**
     * @inheritdoc
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function count(BookSearch $bookSearch): int
    {
        $result = $this->client->count([
            'index' => $this->config->getStorageName(),
            'body' => BookQuery::create($bookSearch),
        ])->asArray();

        return $result['count'];
    }
}
