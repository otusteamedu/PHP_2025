<?php

declare(strict_types=1);

namespace Otus\DataMapper\Presentation\Console;

use Otus\DataMapper\Application\UseCase\Product\DeleteProductUseCase;

final readonly class DeleteProductCommand
{
    /**
     * @param DeleteProductUseCase $deleteProductUseCase
     */
    public function __construct(
        private DeleteProductUseCase $deleteProductUseCase
    ) {
    }

    /**
     * @return int
     */
    public function __invoke(): int
    {
        $result = $this->deleteProductUseCase->execute(1);

        if ($result === false) {
            echo 'Not found.' . PHP_EOL;

            return 1;
        }

        echo 'Success' . PHP_EOL;

        return 0;
    }
}
