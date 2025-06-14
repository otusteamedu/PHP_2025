<?php

declare(strict_types=1);

namespace Infrastructure\Rabbit;

use App\Helper;
use Domain\Models\Order;
use Exception;
use PhpAmqpLib\Message\AMQPMessage;

class Publisher
{
    /**
     * @throws Exception
     */
    public function publish(Order $order)
    {
        $env = Helper::getEnv();
        $connection = new Connection($env);

        $channel = $connection->getChannel();

        $channel->queue_declare($env['RABBITMQ_QUEUE'], false, true, false, false);

        $messageBody = json_encode([
            'order' => [
                $order->toArray()
            ],
            'timestamp' => date('Y-m-d H:i:s')
        ]);

        $msg = new AMQPMessage($messageBody, [
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
        ]);

        $channel->basic_publish($msg, '', $env['RABBITMQ_QUEUE']);

        $channel->close();
        $connection->close();
    }
}