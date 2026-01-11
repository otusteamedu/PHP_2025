<?php declare(strict_types=1);

namespace Fastfood\Orders;

use Fastfood\Orders\Observers\OrderObserverInterface;
use Fastfood\Orders\Observers\OrderSubjectInterface;
use Fastfood\Products\Entity\ProductInterface;

class Order implements OrderSubjectInterface
{
    private int $id;
    private string $status = 'new';
    private array $products = [];
    private array $productsData = [];
    private array $observers = [];
    private float $totalCost = 0.0;

    public function __construct()
    {
        $this->id = rand(1000, 9999);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getProducts(): array
    {
        return $this->products;
    }

    public function getProductsData(): array
    {
        return $this->productsData;
    }

    public function getTotalCost(): float
    {
        if ($this->totalCost > 0.0) { return $this->totalCost; }

        foreach ($this->products as $product) {
            $this->totalCost += $product->getCost();
        }
        return $this->totalCost;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function setProductsData(array $productsData): void
    {
        $this->productsData = $productsData;
    }

    public function setTotalCost(float $totalCost): void
    {
        $this->totalCost = $totalCost;
    }

    public function addProduct(ProductInterface $product): void
    {
        $this->products[] = $product;
    }

    public function attach(OrderObserverInterface $observer): void
    {
        $this->observers[] = $observer;
    }

    public function detach(OrderObserverInterface $observer): void
    {
        $this->observers = array_filter($this->observers, function($obs) use ($observer) {
            return $obs !== $observer;
        });
    }

    public function notify(string $event): void
    {
        foreach ($this->observers as $observer) {
            $observer->update($this, $event);
        }
    }
}