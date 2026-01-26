<?php

declare(strict_types=1);

namespace Otus\DataMapper\Presentation\Console;

use Otus\DataMapper\Application\UseCase\Product\GetProductsUseCase;
use Otus\DataMapper\Domain\Entity\Product;

final readonly class ListProductsCommand
{
    /**
     * @param GetProductsUseCase $getProductsUseCase
     */
    public function __construct(
        private GetProductsUseCase $getProductsUseCase
    ) {
    }

    /**
     * @return int
     */
    public function __invoke(): int
    {
        $products = $this->getProductsUseCase->execute();

        foreach ($products as $product) {
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
