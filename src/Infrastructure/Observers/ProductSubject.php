<?php

namespace App\Domain\Observable;

use App\Domain\Interfaces\ProductObserverInterface;
use App\Domain\Entities\Product;
use App\Domain\Enums\ProductStatus;

class ProductSubject
{
    /** @var array<ProductObserverInterface> */
    private array $observers = [];
    private ProductStatus $previousStatus;

    public function attach(ProductObserverInterface $observer): void
    {
        $this->observers[] = $observer;
    }

    public function detach(ProductObserverInterface $observer): void
    {
        $this->observers = array_filter(
            $this->observers,
            fn($obs) => $obs !== $observer
        );
    }

    public function setProductStatus(Product $product, ProductStatus $status): void
    {
        $this->previousStatus = $product->getStatus();
        $product->setStatus($status);
        $this->notify($product);
    }

    private function notify(Product $product): void
    {
        foreach ($this->observers as $observer) {
            $observer->update($product, $this->previousStatus);
        }
    }
}