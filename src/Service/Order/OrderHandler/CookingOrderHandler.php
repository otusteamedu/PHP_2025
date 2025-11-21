<?php
declare(strict_types=1);

namespace Dinargab\Homework15\Service\Order\OrderHandler;


use Dinargab\Homework15\Model\Order\OrderStatus;
use Dinargab\Homework15\Model\Order\ProductOrder;
use Dinargab\Homework15\Service\Cooking\CookProcessInterface;

class CookingOrderHandler extends AbstractOrderHandler
{
    public function __construct(private CookProcessInterface $process)
    {
        parent::__construct();
    }

    public function handle(ProductOrder $order): void
    {
        $order->setStatus(OrderStatus::COOKING);
        foreach ($order->getOrderedProducts() as $product) {
            $order->addProduct($this->process->cook($product['type'], $product['additionalIngredients']));
        }
        parent::handle($order);
    }
}