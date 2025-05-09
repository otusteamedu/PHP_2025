<?php

namespace Application\UseCase\AddNews;

// данные в виде DTO 

class MakeProductRequest
{  
    public function __construct(  
        public readonly string $url // То что будем отправлять в базу 
    )  
    {  
    }  
}