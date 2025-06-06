<?php

namespace App\Domain\Entity\PayWay;

use App\Domain\Factory\PayWay\PayWayFactoryInterface;

class CardPayWay implements PayWayFactoryInterface
{  

    public function getPayWay(): ?string  
    {  
        return "By card";  
    }

}