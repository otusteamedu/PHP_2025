<?php

declare(strict_types=1);

namespace Otus\DataMapper\Command;

use Otus\DataMapper\Entity\Product;
use Otus\DataMapper\Factory\RepositoryFactory;
use Otus\DataMapper\Repository\ProductRepository;

readonly class InsertCommand
{
    /**
     * @return int
     */
    public function __invoke(): int
    {
        /** @var ProductRepository $repository */
        $repository = RepositoryFactory::factory(Product::class);

        $list = [
            [
                'brand' => 'Apple',
                'title' => 'Macbook',
                'price' => 2000,
                'capacity' => 1,
            ],
            [
                'brand' => 'DELL',
                'title' => 'XPS',
                'price' => 1000,
                'capacity' => 10,
            ],
        ];

        foreach ($list as $row) {
            $product = new Product(...$row);

            $repository->insert($product);

            $this->render($product);
        }

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
