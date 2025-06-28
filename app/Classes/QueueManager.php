<?php
namespace App\Classes;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class QueueManager {
    // создаём подключение к RabbitMQ
    public static function getConnection() {
        return new AMQPStreamConnection(
            $_ENV['RABBIT_HOST'],
            $_ENV['RABBIT_PORT'],
            $_ENV['RABBIT_USER'],
            $_ENV['RABBIT_PASS']
        );
    }

    public static function publish(array $data) {
        $conn = self::getConnection(); 
        $channel = $conn->channel();
        $channel->queue_declare('report_queue', false, false, false, false);  //открываем канал с именем report_queue
        $msg = new AMQPMessage(json_encode($data));   //упаковываем новое сообщение
        $channel->basic_publish($msg, '', 'report_queue');  //отправляем сообщиние в очередь
        $channel->close(); //закрываем канал
        $conn->close();    //закрываем соединение
    }

    public static function consume(callable $callback) {  
        $conn = self::getConnection();  //открываем соедиенение заново для чтения из очереди
        $channel = $conn->channel();
        $channel->queue_declare('report_queue', false, false, false, false);
        $channel->basic_consume('report_queue', '', false, true, false, false, function ($msg) use ($callback) {
            $callback(json_decode($msg->body, true)); //колбэк функция которая выззывается при получении нового сообщения
        });
        while ($channel->is_consuming()) { //в бесконечном цикле ожидаем новых сообщений из очереди
            $channel->wait();
        }
    }
}