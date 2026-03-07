<?php

declare(strict_types=1);

namespace Otus\Queue\Application\Amqp;

use PhpAmqpLib\Message\AMQPMessage;

class Chat
{
    /**
     * @param AMQPMessage $message
     */
    public function __invoke(AMQPMessage $message): void
    {
        $message->ack();

        echo 'data: ' . $message->getBody() . "\n\n";

        if (ob_get_level() > 0) {
            ob_flush();
        }

        flush();
    }
}
