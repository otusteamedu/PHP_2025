<?php

namespace Application\UseCase\GetNews;

// данные в виде DTO 

class GetNewsRequest
{  
    public function __construct(  
        public readonly array $id_array // То что будем отправлять в базу 
    )  
    {  
    }  
}