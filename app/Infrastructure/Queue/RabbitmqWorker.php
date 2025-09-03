<?php

namespace App\Infrastructure\Queue;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use App\Application\UseCases\Commands\SendEmail\Handler;
use App\Application\UseCases\Commands\QueueWorkerInterface;

class RabbitmqWorker implements QueueWorkerInterface
{
    private $sendEmailHandler;

    public function __construct()
    {
        $this->sendEmailHandler = new Handler();
    }

    public function sendMessage($request): void
    {
        $content = json_encode($request);

        $host = getenv('RABBITMQ_HOST');
        $port = getenv('RABBITMQ_PORT');
        $username = getenv('RABBITMQ_USERNAME');
        $password = getenv('RABBITMQ_PASSWORD');

        $connection = new AMQPStreamConnection($host, $port, $username, $password);
        $channel = $connection->channel();

        $channel->queue_declare('applications', false, false, false, false);

        $msg = new AMQPMessage($content);
        $channel->basic_publish($msg, '', 'applications');

        $channel->close();
        $connection->close();
    }

    public function runReceiver(): void
    {
        $host = getenv('RABBITMQ_HOST');
        $port = getenv('RABBITMQ_PORT');
        $username = getenv('RABBITMQ_USERNAME');
        $password = getenv('RABBITMQ_PASSWORD');

        $connection = new AMQPStreamConnection($host, $port, $username, $password);
        $channel = $connection->channel();

        $channel->queue_declare('applications', false, false, false, false);

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

            try {
                $this->sendEmailHandler->handle($email, $startDate, $finishDate);
                echo " [x] The request has been processed. The email message has been sent.\n\n";
            } catch (\Exception $e) {
                echo " [x] Message could not be sent. Mailer Error.\n\n";
            }
        };

        $channel->basic_consume('applications', '', false, true, false, false, $callback);

        try {
            $channel->consume();
        } catch (\Throwable $exception) {
            echo $exception->getMessage();
        }
    }
}