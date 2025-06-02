<?php

namespace App\Classes;

use Symfony\Component\Dotenv\Dotenv;

class App
{
    public function run()
    {
        $dotenv = new Dotenv();
        $dotenv->load($_SERVER['DOCUMENT_ROOT'].'/.env');

        if (!isset($_REQUEST['date_from'])) throw new \RuntimeException('Date from didn`t send');
        if (!isset($_REQUEST['date_to'])) throw new \RuntimeException('Date to didn`t send');

        $message = 'Отправлен в очередь запрос на генерацию финансового отчета с '.$_REQUEST['date_from'].' по '.$_REQUEST['date_to'];
        print_r($message);

        $RabbitMQManager = new RabbitMQManager();
        $RabbitMQManager->pushMessage($message);

        $telegramService = new TelegramService();
        $telegramService->sendNotification($message);
    }
}