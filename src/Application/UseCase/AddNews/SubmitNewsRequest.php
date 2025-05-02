<?php

namespace Application\UseCase\AddNews;

class SubmitNewsRequest
{  
    public function __construct(  
        public readonly string $url
    )  
    {  
    }  
}