<?php

namespace Application\UseCase\GetAllNews;

class GetAllNewsResponse 
{

    public function __construct(  
        public readonly array $allNews
    )  
    {  
    }  

    public function getAllNews() {
        return $this->allNews; 
    }

}