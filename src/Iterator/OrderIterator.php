<?php

namespace App\Iterator;
use Iterator;
use App\Model\Order;

class OrderIterator implements Iterator {
    private $position = 0;
    private $statusFlow = ['создан', 'подготовка ингредиентов', 'приготовление',  'готов', 'доставлен'];
    
    public function __construct(private Order $order) {
        $this->order->setStatus($this->current());
    }
    
    public function current(): string 
    {
        return $this->statusFlow[$this->position];
    }
    
    public function next(): void 
    {
        $this->position++;
        if ($this->valid()) {
            $this->order->setStatus($this->current());
        }
    }
    
    public function key(): int 
    {
        return $this->position;
    }
    
    public function valid(): bool 
    {
        return isset($this->statusFlow[$this->position]);
    }
    
    public function rewind(): void 
    {
        $this->position = 0;
    }
}