<?php

namespace Domain\Entity\OrderCheckout;

class Order  
{  
    private array $order; // Данные заказа
    private string $payway; // Способ оплаты
    private string $getway; // Способ получения

    public function __construct(OrderCheckout $builder)  
    {  
        $this->order = $builder->get_order();  
        $this->payway = $builder->get_payway(); 
        $this->getway = $builder->get_getway(); 
    }  

    public function get_order() 
    {  
        return $this->order;
    } 

    public function get_payway() 
    {  
        return $this->payway;
    }  

    public function get_getway() 
    {  
        return $this->getway;
    }  
     
}