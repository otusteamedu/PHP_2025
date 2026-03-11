<?php
declare(strict_types=1);

namespace Pryaniki\App\Presentation\Controllers\Commands\Elasticsearch;

use Pryaniki\App\Application\DTO\SearchProductDTO;
use Pryaniki\App\Application\UseCase\SearchProductUseCase;
use Pryaniki\App\Domain\Repositories\ProductRepositoryInterface;

class SearchAction
{
    protected ProductRepositoryInterface $repository;

    public function __construct(ProductRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    public function run(array $args): array
    {
        $dto = SearchProductDTO::fromArray($args);
        $useCase = new SearchProductUseCase($this->repository);
        return $useCase->execute($dto);
    }
}