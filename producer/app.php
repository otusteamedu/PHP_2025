
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
$exchange = new AMQPExchange($channel);
$exchange->setName('hash_exchange');
$exchange->setType('x-consistent-hash');
$exchange->setArgument('hash-header', 'hash-on');
$exchange->declareExchange();

$queue = new AMQPQueue($channel);
$queue->setName('queue_1');
$queue->declareQueue();
$queue->bind('hash_exchange', '1'); // вес

$queue = new AMQPQueue($channel);
$queue->setName('queue_2');
$queue->declareQueue();
$queue->bind('hash_exchange', '1'); // вес

for ($i = 0; $i < 10000; $i++) {
    $key = rand(1, 100);
    $exchange->publish($key, '', AMQP_NOPARAM, [
        'headers' => ['hash-on' => "user$i"]
    ]);
    echo "Sent message $i with key $key\n";
    sleep(1);
}
?>
