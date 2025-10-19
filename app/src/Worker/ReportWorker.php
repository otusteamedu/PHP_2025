<?php

namespace App\Worker;

use App\Shared\RabbitMQ\RabbitMQConnection;
use Exception;
use PhpAmqpLib\Message\AMQPMessage;
use PHPMailer\PHPMailer\PHPMailer;

require __DIR__ . '/../../vendor/autoload.php';

$connection = new RabbitMQConnection('rabbitmq', 5672, 'guest', 'guest');
$channel = $connection->getChannel();

$channel->queue_declare('report_queue', false, true, false, false);

echo " Ожидание сообщений...\n";

$channel->basic_consume('report_queue', '', false, true, false, false, function (AMQPMessage $msg) {
    $data = json_decode($msg->body, true);
    echo "Обработка запроса: ", print_r($data, true);

    sleep(3);

    if ($data['notify']['type'] === 'email') {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'marinalubnik@gmail.com';
            $mail->Password = 'Password';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('marinalubnik@gmail.com', 'Report Bot');
            $mail->addAddress($data['notify']['address']);
            $mail->Subject = 'Ваш отчёт готов';
            $mail->Body = 'Отчёт успешно сформирован.';

            $mail->SMTPDebug = 2;
            $mail->Debugoutput = 'echo';
            $mail->send();
            echo "Email отправлен\n";
        } catch (Exception) {
            echo "⚠️ Ошибка отправки email: {$mail->ErrorInfo}\n";
        }
    }

    echo "Уведомление отправлено\n";
});

while ($channel->is_consuming()) {
    $channel->wait();
}
