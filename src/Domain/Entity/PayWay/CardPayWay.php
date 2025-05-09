<?php

namespace Domain\Entity\PayWay;

use Domain\Factory\PayWay\PayWayFactoryInterface;

class CardPayWay implements PayWayFactoryInterface
{  

    public function getPayWay(): ?string  
    {  
        return "By card";  
    }

}