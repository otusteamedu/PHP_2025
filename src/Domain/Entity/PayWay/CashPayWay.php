<?php

namespace Domain\Entity\PayWay;

use Domain\Factory\PayWay\PayWayFactoryInterface;

class CashPayWay implements PayWayFactoryInterface
{  

    public function getPayWay(): ?string  
    {  
        return "By cash";  
    }

}