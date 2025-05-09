<?php

namespace Domain\Entity\GetWay;

use Domain\Factory\GetWay\GetWayFactoryInterface;

class HomeGetWay implements GetWayFactoryInterface
{  

    public function getGetWay(): ?string  
    {  
        return "To home";  
    }

}