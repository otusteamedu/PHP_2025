<?php

namespace App\Domain\Entity\GetWay;

use App\Domain\Factory\GetWay\GetWayFactoryInterface;

class HomeGetWay implements GetWayFactoryInterface
{  

    public function getGetWay(): ?string  
    {  
        return "To home";  
    }

}