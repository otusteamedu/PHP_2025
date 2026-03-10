<?php
declare(strict_types=1);

namespace Pryaniki\App\Application\UseCase;

use Pryaniki\App\Application\DTO\SearchProductDTO;
use Pryaniki\App\Domain\Repositories\ProductRepositoryInterface;

class SearchProductUseCase
{
    private ProductRepositoryInterface $repository;

    public function __construct(ProductRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(SearchProductDTO $dto): array
    {
        return $this->repository->search($dto);
    }
}