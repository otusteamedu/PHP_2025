<?php

declare(strict_types=1);

namespace Otus\DataMapper\Command;

use Otus\DataMapper\Entity\Product;
use Otus\DataMapper\Factory\RepositoryFactory;
use Otus\DataMapper\Repository\ProductRepository;
use Otus\DataMapper\Sql\Command;
use Otus\DataMapper\Sql\Condition\Equal;
use Otus\DataMapper\Sql\Where;

readonly class UpdateCommand
{
    /**
     * @return int
     */
    public function __invoke(): int
    {
        /** @var ProductRepository $repository */
        $repository = RepositoryFactory::factory(Product::class);

        $command = new Command()
            ->where(
                new Where(
                    new Equal('id', 1)
                )
            );

        $result = $repository->eager($command);

        $product = $result->current();

        $product->price = 1000;

        $repository->update($product);

        $this->render($product);

        return 0;
    }

    /**
     * @param Product $product
     */
    protected function render(Product $product): void
    {
        print_r($product);
    }
}
