<?php

declare(strict_types=1);

namespace Otus\DataMapper\Command;

use Otus\DataMapper\Condition\Equal;
use Otus\DataMapper\Entity\Product;
use Otus\DataMapper\Factory\RepositoryFactory;
use Otus\DataMapper\Repository\ProductRepository;

readonly class UpdateCommand
{
    /**
     * @return int
     */
    public function __invoke(): int
    {
        /** @var ProductRepository $repository */
        $repository = RepositoryFactory::factory(Product::class);

        $result = $repository->eager(new Equal('id', 1));

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
