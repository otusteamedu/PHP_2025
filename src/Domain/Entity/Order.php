<?php

namespace Domain\Entity;

use \Domain\ValueObject\User;

class Order 
{  
    private ?int $id = null; 
  
    public function __construct(  
        private User $user
    )  
    {  
    }  

    public function getId(): ?int  
    {  
        return $this->id;  
    }  
}