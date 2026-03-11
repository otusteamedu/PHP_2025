<?php
declare(strict_types=1);

namespace App\Infrastructure\RabbitMq;

use PhpAmqpLib\Message\AMQPMessage;

class AmqpMessageFactory
{
    private const array DEFAULT_OPTIONS = [
        'content_type' => 'application/json',
        'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
    ];

    public static function createMessage(string $messageBody, array $options = []): AMQPMessage
    {
        return new AMQPMessage(
            $messageBody,
            array_merge(
                self::DEFAULT_OPTIONS,
                $options,
                [
                    'timestamp' => time(),
                    'message_id' => bin2hex(random_bytes(8)),
                ]
            )
        );
    }
}
