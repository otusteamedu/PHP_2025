<?php

namespace App\Domain\Entity\Order;

use App\Domain\ValueObject\User;
use App\Domain\ValueObject\Product;
use App\Domain\Entity\Order\OrderObserver;

class Order 
{  

    public string $status;
  
    public function __construct(  
        private User $user,
        private Product $product
    )  
    {  
        if(empty($this->status))
            self::setStatus("In work");
    }  

    public function getId(): ?string  
    {  
        return session_id();  
    }  

    public function getOrder(): ?array 
    {  
        return $this->product->getValue();  
    }  

    public function getUser(): ?int 
    {  
        return $this->user->getValue();  
    }  

    public function getStatus(): ?string 
    {  
        return $this->status;  
    }  

    public function setStatus($status)
    {  
        $this->status = $status; 
        $observer = new OrderObserver($status);
        $observer->notify($this);  
    }  

}