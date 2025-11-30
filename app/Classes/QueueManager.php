<?php
declare(strict_types=1);

namespace App\Classes;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class QueueManager
{
    /**
     * @throws \Exception
     */
    public static function getConnection(): AMQPStreamConnection
    {
        return new AMQPStreamConnection(
            $_ENV['RABBITMQ_HOST'],
            $_ENV['RABBITMQ_PORT'],
            $_ENV['RABBITMQ_USER'],
            $_ENV['RABBITMQ_PASS']
        );
    }

    /**
     * @throws \JsonException
     * @throws \Exception
     */
    public static function publish(array $arData): void
    {
        $conn = self::getConnection(); 
        $channel = $conn->channel();
        $channel->queue_declare('report_queue', false, false, false, false);
        $msg = new AMQPMessage(json_encode($arData, JSON_THROW_ON_ERROR));
        $channel->basic_publish($msg, '', 'report_queue');
        $channel->close();
        $conn->close();
    }

    /**
     * @throws \JsonException
     * @throws \Exception
     */
    public static function consume(callable $callback): void
    {
        $conn = self::getConnection();
        $channel = $conn->channel();
        $channel->queue_declare('report_queue', false, false, false, false);
        $channel->basic_consume('report_queue', '', false, true, false, false, function ($msg) use ($callback) {
            $callback(json_decode($msg->body, true, 512, JSON_THROW_ON_ERROR));
        });

        while ($channel->is_consuming()) {
            $channel->wait();
        }
    }
}