<?php

declare(strict_types=1);

namespace Otus\DataMapper\Sql\Command;

use Otus\DataMapper\Condition\Condition;
use Otus\DataMapper\Condition\Equal;
use Otus\DataMapper\Entity\Product;
use Otus\DataMapper\Factory\RepositoryFactory;
use Otus\DataMapper\Repository\ProductRepository;

readonly class DeleteCommand
{
    /**
     * @return int
     */
    public function __invoke(): int
    {
        /** @var ProductRepository $repository */
        $repository = RepositoryFactory::factory(Product::class);

        $result = $repository->eager(new Condition('AND', new Equal('brand', 'Apple')));

        $repository->delete($result->current());

        return 0;
    }
}
