<?php

namespace Shop\UseCase;

use Shop\Adapter\Enum\OrderStatus;
use Shop\Observer\Publisher\Publisher;
use Shop\Observer\Subscribers\EmailSubscriber;
use Shop\Order\MealSet;
use Shop\Order\OrderComposite;
use Shop\Strategy\ProductGenerator;

class CreateBusinessLunchUseCase
{
    public function __construct(
        private readonly ProductGenerator $productGenerator,
        private readonly Publisher        $publisher
    )
    {

    }

    public function execute(): OrderComposite
    {
        $emailSubscriber = new EmailSubscriber();
        $this->publisher->subscribe($emailSubscriber, 'order.status.changed');
        $order = new OrderComposite('Заказ № ' . rand(1, 100), $this->publisher);
        $order->setStatus(OrderStatus::PROCESSING);
        $businessLunch = new MealSet('Бизнес-ланч');
        $businessLunch
            ->add($this->productGenerator->getProduct('burger')->cook())
            ->add($this->productGenerator->getProduct('hot_dog')->cook())
            ->add($this->productGenerator->getProduct('pizza')->cook());
        $order->addItem($businessLunch);
        $order->setStatus(OrderStatus::CREATED);
        return $order;
    }
}