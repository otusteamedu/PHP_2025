<?php

declare(strict_types=1);

namespace Otus\DataMapper\Presentation\Console;

use Otus\DataMapper\Application\UseCase\Product\CreateProductUseCase;
use Otus\DataMapper\Domain\Entity\Product;

final readonly class InsertProductCommand
{
    /**
     * @param CreateProductUseCase $createProductUseCase
     */
    public function __construct(
        private CreateProductUseCase $createProductUseCase
    ) {
    }

    /**
     * @return int
     */
    public function __invoke(): int
    {
        $products = [
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

        foreach ($products as $data) {
            $product = $this->createProductUseCase->execute(
                brand: $data['brand'],
                title: $data['title'],
                price: $data['price'],
                capacity: $data['capacity']
            );

            $this->render($product);
        }

        return 0;
    }

    /**
     * @param Product $product
     */
    private function render(Product $product): void
    {
        print_r($product);
    }
}
