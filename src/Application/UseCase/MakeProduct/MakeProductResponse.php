<?php

namespace Application\UseCase\AddNews;

class MakeProductResponse 
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