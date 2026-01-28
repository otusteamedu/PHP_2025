<?php

declare(strict_types=1);

namespace Dinargab\Homework14\Application\UseCase;

use Dinargab\Homework14\Application\DTO\BookDTO;
use Dinargab\Homework14\Application\DTO\GetBySkuRequestDTO;
use Dinargab\Homework14\Domain\Repository\BookSearchRepositoryInterface;
use InvalidArgumentException;

class GetBySkuUseCase
{
    public function __construct(
        private BookSearchRepositoryInterface $bookSearchRepository,
    ) {
    }

    public function __invoke(GetBySkuRequestDTO $getBySkuRequestDTO): ?BookDTO
    {
        if (empty($getBySkuRequestDTO->sku)) {
            throw new InvalidArgumentException('Sku is required.');
        }
        $book = $this->bookSearchRepository->getBySku($getBySkuRequestDTO->sku);

        return BookDTO::fromBook($book);
    }
}