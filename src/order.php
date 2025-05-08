<?php

spl_autoload_register();
session_start();


    

class OrderBuilder  
{  
    private array $product;  
    private string $order; 
    
    public function set_product($product) 
    {  
        if(is_array($product)) {
            $this->product = $product;
            return $this;
        } 

        throw new \Exception("Ошибка продукта");
        
    }  

    public function get_product() 
    {  
        return $this->product;
    }  

    public function set_order($order) 
    {  
        $this->order = $order;
        return $this;
    }  

    public function get_order() 
    {  
        return $this->order;
    } 

    public function build(): Order  
    {  
        return new Order($this);  
    }  
}

class Order  
{  
    private array $product;  
    private string $order; 

    public function __construct(OrderBuilder $builder)  
    {  
        $this->product = $builder->get_product();  
        $this->order = $builder->get_order();  
    }  

    public function get_order() 
    {  
        return $this->order;
    } 

    public function get_product() 
    {  
        return $this->product;
    }  
     
}

try {
    $builder = (new OrderBuilder())->set_product(['Бургер'])->set_order('111'); 
    $person = $builder->build();
    echo "<pre>";
    var_dump($person->get_product());
    echo "</pre>";
}

catch (\Exception $e) {
    echo $e->getMessage();
}

    




