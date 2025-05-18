<?php

namespace App\Application\UseCase\MakeOrder;

class MakeOrderResponse 
{
    public function __construct(  
        public readonly int $id
    )  
    {  
    }  

    public function getId() {
        return $this->id; // id, который придет из базы
    }

}