<?php
require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$data = $_POST;
$startDate = date('d.m.Y', strtotime($data['date_start']));
$finishDate = date('d.m.Y', strtotime($data['date_finish']));
$content = json_encode($data);

$connection = new AMQPStreamConnection('rabbitmq-phpapp', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->queue_declare('applications', false, false, false, false);

$msg = new AMQPMessage($content);
$channel->basic_publish($msg, '', 'applications');

$channel->close();
$connection->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заказ банковской выписки</title>
    <style>
        body {
            font: 16px arial;
        }
        h1, div {
            width: 90%;
            max-width: 450px;
            margin: 40px auto 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>Заявка получена</h1>
    <div>Вы заказали банковскую выписку за <?=$startDate?> - <?=$finishDate?>. Выша заявка обрабатывается. Выписка будет отправлена на указанный Email</div>
</body>
</html>