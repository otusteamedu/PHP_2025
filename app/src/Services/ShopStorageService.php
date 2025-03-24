<?php declare(strict_types=1);

namespace App\Services;

use App\Elastic\Repositories\ShopStorageRepository;
use App\Repositories\ShopStorageRepositoryInterface;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;

/**
 * Class ShopStorageService
 * @package App\Services
 */
class ShopStorageService implements ShopStorageServiceInterface
{
    /**
     * @var ShopStorageRepositoryInterface
     */
    private ShopStorageRepositoryInterface $repository;

    /**
     *
     */
    public function __construct()
    {
        $this->repository = new ShopStorageRepository();
    }

    /**
     * @inheritdoc
     * @throws ClientResponseException
     * @throws MissingParameterException
     * @throws ServerResponseException
     */
    public function create(): array
    {
        return $this->repository->create();
    }

    /**
     * @inheritdoc
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function seed(string $items): array
    {
        return $this->repository->addItems($items);
    }

    /**
     * @inheritdoc
     * @throws ClientResponseException
     * @throws MissingParameterException
     * @throws ServerResponseException
     */
    public function delete(): array
    {
        return $this->repository->delete();
    }
}
