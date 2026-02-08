<?php

namespace Shop\Observer\Subscribers;

use Shop\Ingredients\Component;

class EmailSubscriber implements Subscriber
{

    public function execute(): void
    {
        echo 'Отправка email-сообщений';
    }
}