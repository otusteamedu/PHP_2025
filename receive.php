<?php
require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$connection = new AMQPStreamConnection('rabbitmq-phpapp', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->queue_declare('applications', false, false, false, false);

echo " [*] Waiting for messages. To exit press CTRL+C\n\n";

$callback = function (AMQPMessage $msg) {
    $content = $msg->getBody();
    $data = json_decode($content, true);
    $startDate = date('d.m.Y', strtotime($data['date_start']));
    $finishDate = date('d.m.Y', strtotime($data['date_finish']));
    $email = $data['email'];

    echo " [x] Received request\n";
    echo " [x] Begin of period: $startDate\n";
    echo " [x] End of period: $finishDate\n";
    echo " [x] Email: $email\n\n";

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.mail.ru';
        $mail->SMTPAuth = true;
        $mail->Username = 'elisad5791@mail.ru';
        $mail->Password = 'S0XqMb6kFpe26QUQagGX';
        $mail->Port = 465;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

        $mail->setFrom('elisad5791@mail.ru', 'Mailer');
        $mail->addAddress($email);
        $mail->addReplyTo('elisad5791@mail.ru');

        $mail->isHTML(true);
        $mail->Subject = 'Банковская выписка';
        $mail->Body = "<h1>Банковская выписка</h1><div>Даты: $startDate - $finishDate</div><div>Здесь должны быть данные</div>";
        $mail->AltBody = "Банковская выписка за даты: $startDate - $finishDate\nЗдесь должны быть данные";

        $mail->send();
        echo " [x] The request has been processed. The email message has been sent.\n\n";
    } catch (Exception $e) {
        echo " [x] Message could not be sent. Mailer Error: {$mail->ErrorInfo}\n\n";
    }
};

$channel->basic_consume('applications', '', false, true, false, false, $callback);

try {
    $channel->consume();
} catch (\Throwable $exception) {
    echo $exception->getMessage();
}