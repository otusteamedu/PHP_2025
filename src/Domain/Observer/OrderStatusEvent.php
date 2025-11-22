<?php

namespace Blarkinov\Hw1500\Domain\Observer;

class OrderStatusEvent
{
    public function __construct(private int $id, private string $status) {}

    public function getId():int{
        return $this->id;
    }
    public function getStatus():string{
        return $this->status;
    }
}
