<?php
declare(strict_types=1);

namespace Dinargab\Homework15\Model\Order;


use Dinargab\Homework15\Model\Observer\OrderObserverInterface;
use Dinargab\Homework15\Model\Observer\OrderSubjectInterface;
use Dinargab\Homework15\Model\Order\DTO\ProductOrderNotificationDTO;
use Dinargab\Homework15\Model\Product\ProductInterface;
use SplObjectStorage;

class ProductOrder implements OrderSubjectInterface
{

    private array $products = [];
    private array $orderedProducts = [];
    public OrderStatus $status;
    private SplObjectStorage $observers;
    private int $id;


    public function __construct()
    {
        $this->observers = new SplObjectStorage();
        $this->id = random_int(1, 10_000_000);
    }

    public function getId(): int
    {
        return $this->id;
    }


    public function attach(OrderObserverInterface $observer): void
    {
        $this->observers->attach($observer);
    }

    public function detach(OrderObserverInterface $observer): void
    {
        $this->observers->detach($observer);
    }

    public function notify(): void
    {
        $eventDto = new ProductOrderNotificationDTO(
            $this->id,
            $this->status,
        );
        foreach ($this->observers as $observer) {
            $observer->update($eventDto);
        }
    }

    public function setStatus(OrderStatus $status): void
    {
        $this->status = $status;
        $this->notify();
    }

    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    public function getTotalPrice(): int
    {
        $sum = 0;
        foreach ($this->products as $product) {
            $sum += $product->getPrice();
        }
        return $sum;
    }

    public function setOrderedProducts(array $orderedProducts): ProductOrder
    {
        $this->orderedProducts = $orderedProducts;
        return $this;
    }

    public function getOrderedProducts(): array
    {
        return $this->orderedProducts;
    }

    public function addProduct(ProductInterface $product): ProductOrder
    {
        $this->products[] = $product;
        return $this;
    }

    /**
     * @return array<ProductInterface>
     */
    public function getProducts(): array
    {
        return $this->products;
    }
}