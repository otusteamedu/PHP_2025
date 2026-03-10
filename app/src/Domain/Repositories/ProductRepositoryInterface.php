<?php

namespace Pryaniki\App\Domain\Repositories;

use Pryaniki\App\Application\DTO\SearchProductDTO;

interface ProductRepositoryInterface
{
    public function search(SearchProductDTO $dto): array;
}