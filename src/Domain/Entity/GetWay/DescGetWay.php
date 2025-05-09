<?php

namespace Domain\Entity\GetWay;

use Domain\Factory\GetWay\GetWayFactoryInterface;

class DescGetWay implements GetWayFactoryInterface
{  

    public function getGetWay(): ?string  
    {  
        return "By desc";  
    }

}