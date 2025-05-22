<?php
namespace App\Order;

use SplSubject;
use SplObjectStorage;
use SplObserver;
use App\Products\ProductInterface;

class Order implements OrderInterface
{
    private $observers;
    private $status;
    private $product;
    private $customerName;
    private $deliveryAddress;
    
    public function __construct()
    {
        $this->observers = new SplObjectStorage();
    }
    
    public function attach(SplObserver $observer): void
    {
        $this->observers->attach($observer);
    }
    
    public function detach(SplObserver $observer): void
    {
        $this->observers->detach($observer);
    }
    
    public function notify(): void
    {
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }
    
    public function getStatus(): string
    {
        return $this->status;
    }
    
    public function setStatus(string $status): void
    {
        $this->status = $status;
        $this->notify();
    }
    
    public function setProduct(ProductInterface $product): void
    {
        $this->product = $product;
    }
    
    public function setCustomerName(string $name): void
    {
        $this->customerName = $name;
    }
    
    public function setDeliveryAddress(string $address): void
    {
        $this->deliveryAddress = $address;
    }
}
