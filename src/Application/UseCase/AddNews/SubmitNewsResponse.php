<?php

namespace Application\UseCase\AddNews;

class SubmitNewsResponse 
{
    public function __construct(  
        public readonly int $id
    )  
    {  
    }  

    public function getId() {
        return $this->id;
    }

}