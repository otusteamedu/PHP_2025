<?php

namespace Application\UseCase\MakeOrderCheckout;

use Domain\Entity\OrderCheckout\OrderCheckout;

class MakeOrderCheckoutUseCase
{
    public function __construct(
        private $order,
        private $payway,
        private $getway,
    )
    {
    }

    public function __invoke()
    {
        $builder = 
            (new OrderCheckout())
                ->set_order($this->order->getOrder())
                ->set_payway($this->payway)
                ->set_getway($this->getway)
        ; 
        return $builder->build();
    }
}