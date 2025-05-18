<?php

namespace App\Application\UseCase\MakeOrder;

// данные в виде DTO 

class MakeOrderRequest
{  
    public function __construct(  
        public readonly string $url // То что будем отправлять в базу 
    )  
    {  
    }  
}