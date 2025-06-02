<?php

namespace App\Classes;

use Symfony\Component\Dotenv\Dotenv;

class Consumer
{
    public function consume()
    {
        $dotenv = new Dotenv();
        $dotenv->load($_SERVER['DOCUMENT_ROOT'].'/.env');

        $rabbitMQManager = new RabbitMQManager();
        $rabbitMQManager->consumeMessages();
    }
}