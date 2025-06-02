<?php

namespace App\Classes;

//use PhpAmqpLib\Connection\AMQPConnection;
//use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Dotenv\Dotenv;
use App\Classes\RabbitMQManager;
use App\Classes\TelegramService;

class App
{
    public function run()
    {
        $dotenv = new Dotenv();
        $dotenv->load($_SERVER['DOCUMENT_ROOT'].'/.env');

        //TODO работает
        //$telegramService = new TelegramService();
        //$telegramService->sendNotification('345345435');

        $RabbitMQManager = new RabbitMQManager();
        $RabbitMQManager->pushMessage('TEST_TEST_666666');
    }

}