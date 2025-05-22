<?php
namespace App\Controller;

use App\Services\OrderService;

class OrderController
{
    private $orderService;
    
    public function __construct()
    {
        $this->orderService = new OrderService();
    }
    
    public function processOrder()
    {
        echo "Добро пожаловать в наш интернет-ресторан!\n";
        $this->orderService->createOrder();
    }
}
