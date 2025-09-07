<?php
use App\Infrastructure\Queue\RabbitmqReceiver;

require_once __DIR__ . '/../vendor/autoload.php';

echo " [*] Waiting for messages. To exit press CTRL+C\n\n";

$container = require __DIR__ . '/../bootstrap/dependencies.php';
$worker = $container->get(RabbitmqReceiver::class);
$worker->runReceiver();
