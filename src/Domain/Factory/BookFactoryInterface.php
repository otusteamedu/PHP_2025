<?php

declare(strict_types=1);

namespace Dinargab\Homework14\Domain\Factory;

use Dinargab\Homework14\Application\DTO\BookDTO;
use Dinargab\Homework14\Domain\Entity\Book;

interface BookFactoryInterface
{
    public function create(string $title, string $sku, string $category, int $price, array $stock): Book;

    public function createFromDTO(BookDTO $bookDTO): Book;
}