<?php

declare(strict_types=1);

namespace App\Infrastructure\RabbitMq\Config;

use App\Core\Container\Config\Contracts\DotEnvConfigInterface;
use App\Domain\OrderNotification\ValueObject\Task\NotificationChannel;

class NotificationAmqpConfig
{
    public function __construct(
        private readonly DotEnvConfigInterface $config,
    ) {
    }

    public function exchange(): string
    {
        return (string) $this->config->get('RABBITMQ_ORDER_NOTIFICATIONS_EXCHANGE', 'order.notifications');
    }

    public function emailQueue(): string
    {
        return (string) $this->config->get('RABBITMQ_ORDER_NOTIFICATIONS_EMAIL_QUEUE', 'notifications.email');
    }

    public function smsQueue(): string
    {
        return (string) $this->config->get('RABBITMQ_ORDER_NOTIFICATIONS_SMS_QUEUE', 'notifications.sms');
    }

    public function retryTtlMs(NotificationChannel $channel): int
    {
        $seconds = (int) $this->config->get(
            match ($channel) {
                NotificationChannel::Email => 'RABBITMQ_ORDER_NOTIFICATIONS_EMAIL_RETRY_TTL_SECONDS',
                NotificationChannel::Sms => 'RABBITMQ_ORDER_NOTIFICATIONS_SMS_RETRY_TTL_SECONDS',
            },
            '30',
        );

        return $seconds * 1000;
    }

    public function routingKey(NotificationChannel $channel): string
    {
        return $channel->value;
    }

    public function maxAttempts(): int
    {
        return (int) $this->config->get('RABBITMQ_ORDER_NOTIFICATIONS_MAX_ATTEMPTS', '3');
    }

    public function prefetchCount(): int
    {
        return (int) $this->config->get('RABBITMQ_ORDER_NOTIFICATIONS_PREFETCH_COUNT', '1');
    }
}
