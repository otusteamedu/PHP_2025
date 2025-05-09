<?php

namespace Domain\Entity\OrderCheckout;

use \Domain\ValueObject\User;
use \Domain\ValueObject\Product;
use \Domain\Builder\OrderCheckout\OrderCheckoutBuilderInterface;

class OrderCheckout implements OrderCheckoutBuilderInterface 
{  

    private array $order; // Данные заказа
    private string $payway; // Способ оплаты
    private string $getway; // Способ получения

    public function set_order($order) 
    {  
        $this->order = $order;
        return $this;
    }  

    public function get_order() 
    {  
        return $this->order;
    } 

    public function set_payway($payway) 
    {  
        $this->payway = $payway;
        return $this;
    }  

    public function get_payway() 
    {  
        return $this->payway;
    } 

    public function set_getway($getway) 
    {  
        $this->getway = $getway;
        return $this;
    }  

    public function get_getway() 
    {  
        return $this->getway;
    } 

    public function build(): Order  
    {  
        return new Order($this);  
    }  

}