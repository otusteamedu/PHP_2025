<?php

namespace Application\UseCase\GetNews;

class GetNewsResponse 
{

    public function __construct(  
        public readonly array $news
    )  
    {  
    }  

    public function getNews() {
        return $this->news; 
    }

}