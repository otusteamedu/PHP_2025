<?php

namespace App\Service;

use App\Factory\ProductFactory;
use App\Model\Order;
use App\Proxy\QualityControlProxy;

class OrderService 
{    
    public function __construct(
        private ProductFactory $factory, 
        private QualityControlProxy $proxy
    ) {}
    
    public function createOrder(array $items): Order 
    {
        $order = new Order();
        
        foreach ($items as $item) {
            $type = $item['type'];
            $additives = $item['additives'] ?? [];
            $product = $this->factory->create($type, $additives);
            $preparedProduct = $this->proxy->prepare($product);
            $order->addItem($preparedProduct);
        }
        
        return $order;
    }
}