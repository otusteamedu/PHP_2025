<?php

namespace Domain\Entity;

use \Domain\ValueObject\Url;

class News  
{  
    private ?int $id = null;  
  
    public function __construct(  
        private Url $url
    )  
    {  
    }  

    public function getId(): ?int  
    {  
        return $this->id;  
    }  
  
    public function getUrl(): Url  
    {  
        return $this->url;  
    }  
}