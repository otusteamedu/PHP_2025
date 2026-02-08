<?php

namespace Shop\UseCase;

use Shop\Adapter\Enum\OrderStatus;
use Shop\Observer\Publisher\Publisher;
use Shop\Observer\Publisher\SmsPublisher;
use Shop\Observer\Subscribers\EmailSubscriber;
use Shop\Observer\Subscribers\SmsSubscriber;
use Shop\Order\MealSet;
use Shop\Order\OrderComposite;
use Shop\Strategy\ProductGenerator;

class CreateOrderUseCase
{
    public function __construct(
        private readonly ProductGenerator $productGenerator,
        private readonly Publisher        $publisher
    )
    {

    }

    public function execute(array $listItems): OrderComposite
    {
        $smsSubscriber = new SmsSubscriber();
        $emailSubscriber = new EmailSubscriber();
        $this->publisher->subscribe($smsSubscriber, 'order.status.changed');
        $this->publisher->subscribe($emailSubscriber, 'order.status.changed');

        $order = new OrderComposite('Заказ № ' . rand(1, 100), $this->publisher);

        $order->setStatus(OrderStatus::PROCESSING);

        foreach ($listItems as $item) {
            $order->addItem($this->productGenerator->getProduct($item['name'])->cook($item['additional_ingredients'] ?? []));
        }
        $order->setStatus(OrderStatus::CREATED);
        return $order;
    }
}