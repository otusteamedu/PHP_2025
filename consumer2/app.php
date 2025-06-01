<?php
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
$queue->setName('queue_2');
$queue->declareQueue();
$queue->bind('hash_exchange', '1'); // вес очереди

echo "Waiting for messages...\n";

// consume с ручным ack
$queue->consume(function ($envelope, $queue) {
    $body = $envelope->getBody();
    echo "Received: " . $body . "\n";

    // Обработка...

    // Явное подтверждение
    $queue->ack($envelope->getDeliveryTag());
}, AMQP_NOPARAM);
