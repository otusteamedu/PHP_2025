<?php

namespace App\Core\Application;

use App\Shared\RabbitMQ\RabbitMQConnection;
use PhpAmqpLib\Message\AMQPMessage;

class UserReportHandler
{
    public function __invoke(UserReportQuery $query): void
    {
        $connection = new RabbitMQConnection($_ENV['RABBIT_HOST'], $_ENV['RABBIT_PORT'], $_ENV['RABBIT_USER'], $_ENV['RABBIT_PASSWORD']);
        $channel = $connection->getChannel();

        $channel->queue_declare('report_queue', false, true, false, false);

        $data = json_encode([
            'user_id' => $query->userId,
            'interval' => $query->interval,
            'card_id' => $query->cardId,
            'notify' => [
                'type' => 'email',
                'address' => $query->email,
            ]
        ]);

        $msg = new AMQPMessage($data);
        $channel->basic_publish($msg, '', 'report_queue');

        $connection->close();
    }
}
