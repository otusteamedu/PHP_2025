<?php

namespace Consumer;

use AMQPChannel;
use AMQPConnection;
use AMQPExchange;
use AMQPQueue;
use Consumer\Service\MailSender;
use Throwable;

class Kernel
{
    /**
     * Прослушка сообщений и отправка письма
     *
     * @return void
     */
    public function run(): void {
        try {
            $connection = new AMQPConnection([
                'host' => getenv('RABBITMQ_HOST'),
                'port' => 5672,
                'login' => getenv('RABBITMQ_DEFAULT_USER'),
                'password' => getenv('RABBITMQ_DEFAULT_PASS'),
                'vhost' => '/'
            ]);
            $connection->connect();

            $channel = new AMQPChannel($connection);
            $channel->qos(0, 1);

            $exchange = new AMQPExchange($channel);
            $exchange->setName('hash_exchange');
            $exchange->setType('x-consistent-hash');
            $exchange->setArgument('hash-header', 'hash-on');
            $exchange->declareExchange();

            $queue = new AMQPQueue($channel);
            $queue->setName('queue_1');
            $queue->declareQueue();
            $queue->bind('hash_exchange', '1'); // вес очереди

            echo "Прослушка сообщений...\n";

            $queue->consume(function ($envelope, $queue) {
                $body = $envelope->getBody();
                $data = json_decode($body, true);
                echo "Доставлено: " . $body . "\n";

                $sender = new MailSender();
                $sender->send($data);

                $queue->ack($envelope->getDeliveryTag());
            }, AMQP_NOPARAM);
        } catch (Throwable $e) {
            echo $e->getMessage();
        }
    }
}