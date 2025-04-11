<?php

namespace App\Proxy;

use App\DataMappers\OrderMapper;
use App\DB;

class OrderProxy
{

    private array|null $orders = null;

    public function getOrdersByUser(int $userId): array|null
    {
        if ($this->orders == null) {
            $this->orders = $this->directLoadOrders($userId);
        }

        return $this->orders;
    }

    private function directLoadOrders(int $userId): array
    {
        $order = new OrderMapper(DB::getPdo());

        return $order->findByUser($userId);
    }
}
