<?php

declare(strict_types=1);

namespace App\Infrastructure\RabbitMQ;

use App\Application\UseCase\TrainingPlan\GetExercisesByTrainingSchedule;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class EventConsumer
{
    private const QUEUE_NAME = 'fitness';

    public function __construct(
        private readonly AMQPStreamConnection $connection,
        private readonly GetExercisesByTrainingSchedule $getExercisesByTrainingSchedule
    ) {
    }

    public function consume(): void
    {
        $channel = $this->connection->channel();

        $channel->queue_declare(self::QUEUE_NAME, false, true, false, false);

        echo " [*] Waiting for messages. To exit press CTRL+C\n";

        $callback = function (AMQPMessage $msg) {
            echo ' [x] Received ', $msg->body, "\n";
            $event = json_decode($msg->body, true);

            if (
                isset($event['sendAt']) &&
                isset($event['emails']) &&
                isset($event['trainingScheduleId'])
            ) {
                $sendAt = new \DateTime($event['sendAt']);
                $now = new \DateTime();

                if ($sendAt >= $now) {
                    $exercises = $this->getExercisesByTrainingSchedule->execute((int)$event['trainingScheduleId']);
                    
                    foreach ($event['emails'] as $email) {
                        // Here you would implement the email sending logic
                        echo "Sending email to: {$email} with exercises:\n";
                        foreach ($exercises as $exercise) {
                            echo "  - " . $exercise->getTitle() . "\n";
                        }
                    }
                }
            }
        };

        $channel->basic_consume(self::QUEUE_NAME, '', false, true, false, false, $callback);

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $this->connection->close();
    }
}