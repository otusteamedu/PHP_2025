<?php declare(strict_types=1);

namespace App\Services;

use App\Elastic\Repositories\ShopSearchRepository;
use App\Forms\Search\BookSearch;
use App\Repositories\ShopSearchRepositoryInterface;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;

/**
 * Class ShopStorageService
 * @package App\Services
 */
class ShopSearchService implements ShopSearchServiceInterface
{
    /**
     * @var ShopSearchRepositoryInterface
     */
    private ShopSearchRepositoryInterface $repository;

    /**
     *
     */
    public function __construct()
    {
        $this->repository = new ShopSearchRepository();
    }

    /**
     * @inheritdoc
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function search(BookSearch $bookSearch): array
    {
        return $this->repository->search($bookSearch);
    }

    /**
     * @inheritdoc
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function count(BookSearch $bookSearch): int
    {
        return $this->repository->count($bookSearch);
    }
}
