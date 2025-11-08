<?php
namespace App\Order;

interface Order {
    public function process(): void;
}
