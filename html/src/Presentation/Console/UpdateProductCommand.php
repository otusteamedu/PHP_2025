<?php

declare(strict_types=1);

namespace Otus\DataMapper\Presentation\Console;

use Otus\DataMapper\Application\UseCase\Product\UpdateProductUseCase;
use Otus\DataMapper\Domain\Entity\Product;

final readonly class UpdateProductCommand
{
    /**
     * @param UpdateProductUseCase $updateProductUseCase
     */
    public function __construct(
        private UpdateProductUseCase $updateProductUseCase
    ) {
    }

    /**
     * @return int
     */
    public function __invoke(): int
    {
        $product = $this->updateProductUseCase->execute(1, [
            'price' => 1000,
        ]);

        $this->render($product);

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
