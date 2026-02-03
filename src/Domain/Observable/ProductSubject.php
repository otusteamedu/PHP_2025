<?php

namespace App\Domain\Observable;

use App\Domain\Interfaces\ProductObserverInterface;
use App\Domain\Entities\Product;
use App\Domain\Enums\ProductStatus;

class ProductSubject
{
    /** @var array<ProductObserverInterface> */
    private array $observers = [];
    private ?ProductStatus $previousStatus = null;

    public function attach(ProductObserverInterface $observer): void
    {
        if (!in_array($observer, $this->observers, true)) {
            $this->observers[] = $observer;
        }
    }

    public function detach(ProductObserverInterface $observer): void
    {
        $key = array_search($observer, $this->observers, true);
        if ($key !== false) {
            unset($this->observers[$key]);
            $this->observers = array_values($this->observers);
        }
    }

    public function setProductStatus(Product $product, ProductStatus $status): void
    {
        $this->previousStatus = $product->getStatus();
        $product->setStatus($status);
        $this->notify($product);
    }

    private function notify(Product $product): void
    {
        if ($this->previousStatus === null) {
            return;
        }

        foreach ($this->observers as $observer) {
            $observer->update($product, $this->previousStatus);
        }
    }
}