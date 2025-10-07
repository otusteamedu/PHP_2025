<?php

namespace App;

use App\Products\BaseProduct;
use App\Observers\CookingObserverInterface;
use App\Decorators\ProductDecoratorInterface;

class Kitchen
{
    private array $observers = [];
    private BaseProduct $currentProduct;

    public function setProduct(BaseProduct $product): void
    {
        $this->currentProduct = $product;
    }

    public function addObserver(CookingObserverInterface $observer): void
    {
        $this->observers[] = $observer;
    }

    public function removeObserver(CookingObserverInterface $observer): void
    {
        $this->observers = array_filter($this->observers, function ($obs) use ($observer) {
            return $obs !== $observer;
        }); 
    }

    public function changeStatus(string $newStatus): void
    {
        $oldStatus = $this->currentProduct->getStatus();
        $this->currentProduct->setStatus($newStatus);
        $this->notifyObservers($oldStatus, $newStatus);
    }

    public function applyDecorator(ProductDecoratorInterface $decorator): void
    {
        $this->currentProduct = $decorator->decorate($this->currentProduct);
    }

    private function notifyObservers(string $oldStatus, string $newStatus): void
    {
        foreach ($this->observers as $observer) {
            $observer->update($this->currentProduct, $oldStatus, $newStatus);
        }
    }

    public function getCurrentProduct(): BaseProduct
    {
        return $this->currentProduct;
    }
}