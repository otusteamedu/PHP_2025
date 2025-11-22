<?php

namespace Blarkinov\Hw1500\Application\Gateway;

use Blarkinov\Hw1500\Application\Composite\Order;

interface DataBaseGateway
{
    public function setOrder(Order $order):int;
    public function getOrder(int $id): ?Order;

    public function getCountOrder():int;
}
