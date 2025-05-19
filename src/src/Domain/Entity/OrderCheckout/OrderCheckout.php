<?php

namespace App\Domain\Entity\OrderCheckout;

use App\Domain\ValueObject\User;
use App\Domain\ValueObject\Product;
use App\Domain\Builder\OrderCheckout\OrderCheckoutBuilderInterface;

class OrderCheckout implements OrderCheckoutBuilderInterface 
{  

    private array $order; // Данные заказа
    private string $payway; // Способ оплаты
    private string $getway; // Способ получения

    public function set_order($order) 
    {  
        if(!$order)
            throw new \Exception("Ошибка массива");

        $this->order = $order;
        return $this;
    }  

    public function get_order() 
    {  
        return $this->order;
    } 

    public function set_payway($payway) 
    {  
        if(!$payway)
            throw new \Exception("Ошибка способа оплаты");

        $this->payway = $payway;
        return $this;
    }  

    public function get_payway() 
    {  
        return $this->payway;
    } 

    public function set_getway($getway) 
    {  
        if(!$getway)
            throw new \Exception("Ошибка способа доставки");

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