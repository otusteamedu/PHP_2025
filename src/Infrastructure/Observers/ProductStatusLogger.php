<?php

namespace App\Infrastructure\Observers;

use App\Domain\Interfaces\ProductObserverInterface;
use App\Domain\Entities\Product;
use App\Domain\Enums\ProductStatus;
use Psr\Log\LoggerInterface;

class ProductStatusLogger implements ProductObserverInterface
{
    public function __construct(
        private LoggerInterface $logger
    ) {}

    public function update(Product $product, ProductStatus $previousStatus): void
    {
        $this->logger->info('Product status changed', [
            'product_id' => $product->getId(),
            'product_name' => $product->getName(),
            'previous_status' => $previousStatus->value,
            'new_status' => $product->getStatus()->value,
            'time' => date('Y-m-d H:i:s')
        ]);
    }
}