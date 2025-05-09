<?php

namespace Application\UseCase\MakeOrder;

class MakeOrderUseCase
{
    public function __construct(
        private $order,
    )
    {
    }

    public function __invoke()
    {
        return [
            "order_id"=>$this->order->getId(),
            "order_data"=>$this->order->getOrder(),
            "order_user_id"=>$this->order->getUser(),
            "order_status"=>$this->order->getStatus()
        ]; 
    }
}