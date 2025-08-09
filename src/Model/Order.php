<?php

namespace App\Model;

class Order {
    private $items = [];
    private $status;
    private $statusHistory = [];
    
    public function addItem(Product $item): void 
    {
        $this->items[] = $item;
    }
    
    public function getItems(): array 
    {
        return $this->items;
    }
    
    public function getTotal(): float 
    {
        return array_reduce($this->items, function($sum, $item) {
            return $sum + $item->getPrice();
        }, 0);
    }
    
    public function setStatus(string $status): void 
    {
        $this->statusHistory[] = $this->status;
        $this->status = $status;
    }
    
    public function getStatus(): string 
    {
        return $this->status;
    }
    
    public function getStatusHistory(): array 
    {
        return $this->statusHistory;
    }
}