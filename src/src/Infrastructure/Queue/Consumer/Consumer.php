<?php

class Consumer {

    public function __invoke() {
        
        $conn = new AMQPConnection([
            'host' => 'rabbitmq',
            'port' => 5672,
            'login' => 'guest',
            'password' => 'guest',
            'vhost' => '/'
        ]);
        $conn->connect();

        $channel = new AMQPChannel($conn);

        // Устанавливаем QoS: prefetch_count = 1
        $channel->qos(0, 1); // prefetch size = 0, count = 1

        $exchange = new AMQPExchange($channel);
        $exchange->setName('hash_exchange');
        $exchange->setType('x-consistent-hash');
        $exchange->setArgument('hash-header', 'hash-on');
        $exchange->declareExchange();

        $queue = new AMQPQueue($channel);
        $queue->setName('queue_1');
        $queue->declareQueue();
        $queue->bind('hash_exchange', '1'); // вес очереди

        echo "Ждем сообщения...\n";

        // consume с ручным ack
        $queue->consume(function ($envelope, $queue) {
            $body = $envelope->getBody();
            echo "Принято: " . $body . "\n";
            $data = json_decode($body,true);
            echo "Обрабатываем 60 секунд...\n";
            sleep(60);
            // Удалим файл из хранилища
            (new FileStorage())($data["id"]);
            // Явное подтверждение
            $queue->ack($envelope->getDeliveryTag());
        }, AMQP_NOPARAM);

    }

}


