<?php

spl_autoload_register();
session_start();


    

class OrderBuilder  
{   
    private string $order; // Данные заказа
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

class Order  
{  
    private string $order; // Данные заказа
    private string $payway; // Способ оплаты
    private string $getway; // Способ получения

    public function __construct(OrderBuilder $builder)  
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

try {
    $builder = (new OrderBuilder())->set_order('Бургер')->set_payway('Картой')->set_getway('На кассе'); 
    $order_build = $builder->build();
    echo "<pre>";
    var_dump($order_build);
    echo "</pre>";
}

catch (\Exception $e) {
    echo $e->getMessage();
}

    




