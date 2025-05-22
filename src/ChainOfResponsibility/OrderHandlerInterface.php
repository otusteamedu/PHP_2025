<?php
namespace App\ChainOfResponsibility;

use App\Order\OrderInterface;

interface OrderHandlerInterface
{
    public function setNext(OrderHandlerInterface $handler): void;
    public function handle(OrderInterface $order): void;
}
