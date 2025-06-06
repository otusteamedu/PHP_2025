<?php

namespace Src\Infrastructure\Queue\Producer; 

class Producer {

    public function __invoke($json) {
        
        $conn = new \AMQPConnection([
            'host' => 'rabbitmq',
            'port' => 5672,
            'login' => 'guest',
            'password' => 'guest',
            'vhost' => '/'
        ]);
        $conn->connect();

        $channel = new \AMQPChannel($conn);
        $exchange = new \AMQPExchange($channel);
        $exchange->setName('hash_exchange');
        $exchange->setType('x-consistent-hash');
        $exchange->setArgument('hash-header', 'hash-on');
        $exchange->declareExchange();

        $queue = new \AMQPQueue($channel);
        $queue->setName('queue_1');
        $queue->declareQueue();
        $queue->bind('hash_exchange', '1');

        $exchange->publish($json, '', AMQP_NOPARAM, [
            'headers' => ['hash-on' => "date"]
        ]);
        echo "<p>Sent message</p>";

       /*  for ($i = 0; $i < 5; $i++) {
            $key = rand(1, 100);
            $exchange->publish($key."email", '', AMQP_NOPARAM, [
                'headers' => ['hash-on' => "user$i"]
            ]);
            echo "<p>Sent message $i with key $key</p>";
            sleep(1);
        } */

    }

}
