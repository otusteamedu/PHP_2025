<?php

namespace Application\Event;

use AMQPChannelException;
use AMQPConnectionException;
use AMQPEnvelopeException;
use AMQPExchangeException;
use AMQPQueueException;
use Domain\Receiver\ReceiverInterface;
use Throwable;

class EventConsumeUseCase
{
    private ReceiverInterface $receiver;

    public function __construct(ReceiverInterface $receiver) {
        $this->receiver = $receiver;
    }

    /**
     * @return void
     * @throws AMQPChannelException
     * @throws AMQPConnectionException
     * @throws AMQPEnvelopeException
     * @throws AMQPExchangeException
     * @throws AMQPQueueException
     */
    public function run(): void {
        try {
            echo "\nОжидание сообщений...\n";

            $callback = function ($envelope, $queue) {
                $body = $envelope->getBody();

                echo "\nДоставлено: \n" . $body . "\n";

                $queue->ack($envelope->getDeliveryTag());
            };

            $this->receiver->receive($callback);
        } catch (Throwable $e) {
            echo "\nПроизошла ошибка во время получения сообщения\n";
        }
    }
}