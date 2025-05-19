<?php

namespace App\Application\UseCase\MakeOrderCheckout;

use App\Domain\Entity\OrderCheckout\OrderCheckout;
use App\Domain\Entity\Order\Order;
use App\Domain\Factory\PayWay\PayWayFactoryInterface ;
use App\Domain\Factory\GetWay\GetWayFactoryInterface;

class MakeOrderCheckoutUseCase
{
    public function __construct(
        private Order $order,
        private PayWayFactoryInterface $payway,
        private GetWayFactoryInterface $getway,
    )
    {
    }

    public function __invoke()
    {
        $builder = 
            (new OrderCheckout())
                ->set_order($this->order->getOrder())
                ->set_payway($this->payway->getPayWay())
                ->set_getway($this->getway->getGetWay())
        ; 
        return $builder->build();
    }
}