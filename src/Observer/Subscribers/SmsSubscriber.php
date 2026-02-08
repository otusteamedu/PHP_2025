<?php

namespace Shop\Observer\Subscribers;

class SmsSubscriber implements Subscriber
{
    public function execute(): void
    {
        echo 'Отправка sms-сообщений';
    }
}