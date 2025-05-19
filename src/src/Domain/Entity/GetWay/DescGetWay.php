<?php

namespace App\Domain\Entity\GetWay;

use App\Domain\Factory\GetWay\GetWayFactoryInterface;

class DescGetWay implements GetWayFactoryInterface
{  

    public function getGetWay(): ?string  
    {  
        return "By desc";  
    }

}