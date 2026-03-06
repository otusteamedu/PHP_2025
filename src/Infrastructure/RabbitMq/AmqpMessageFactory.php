<?php
declare(strict_types=1);

namespace App\Infrastructure\RabbitMq;

use PhpAmqpLib\Message\AMQPMessage;
use Random\RandomException;

class AmqpMessageFactory
{
    private static array $defaultOptions = [
        'content_type' => 'application/json',
        'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
    ];

    /**
     * @param string $messageBody
     * @param array $options
     * @return AMQPMessage
     * @throws RandomException
     */
    public static function createMessage(string $messageBody, array $options = []): AMQPMessage
    {
        return new AMQPMessage(
            $messageBody,
            array_merge(
                static::$defaultOptions,
                $options,
                ['timestamp' => time(), 'message_id' => bin2hex(random_bytes(8))]
            )
        );
    }
}
