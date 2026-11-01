<?php

declare(strict_types=1);

namespace App\Controller\Http\Api\OrderNotification\Rule;

use App\Controller\Http\Api\OrderNotification\DataObject\CreateNotificationTaskDraft;
use App\Controller\Http\Api\Shared\Validation\ValidationFailedException;
use App\Core\Http\Exception\FieldValidationException;
use App\Domain\OrderNotification\ValueObject\Task\NotificationChannel;

class ChannelRule implements ValidationRuleInterface
{
    public function field(): string
    {
        return 'channel';
    }

    public function expectedType(): string
    {
        return 'string';
    }

    public function apply(int|string $raw, CreateNotificationTaskDraft $draft, ValidationFailedException $failure): void
    {
        $value = strtolower(trim($raw));

        $channel = NotificationChannel::tryFrom($value);

        if ($channel === null) {
            $allowed = implode(', ', NotificationChannel::values());
            $failure->add(new FieldValidationException(
                $this->field(),
                sprintf('Недопустимый канал уведомления: %s. Допустимые каналы: %s', $raw, $allowed),
            ));
            return;
        }

        $draft->channel = $channel;
    }
}
