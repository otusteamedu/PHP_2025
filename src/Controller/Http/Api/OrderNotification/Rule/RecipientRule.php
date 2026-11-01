<?php

declare(strict_types=1);

namespace App\Controller\Http\Api\OrderNotification\Rule;

use App\Controller\Http\Api\OrderNotification\DataObject\CreateNotificationTaskDraft;
use App\Controller\Http\Api\Shared\Validation\ValidationFailedException;
use App\Core\Http\Exception\FieldValidationException;
use App\Domain\OrderNotification\Exception\Input\EmailException;
use App\Domain\OrderNotification\Exception\Input\PhoneException;
use App\Domain\OrderNotification\ValueObject\Recipient\Email;
use App\Domain\OrderNotification\ValueObject\Recipient\Phone;
use App\Domain\OrderNotification\ValueObject\Task\NotificationChannel;

class RecipientRule implements ValidationRuleInterface
{
    public function field(): string
    {
        return 'recipient';
    }

    public function expectedType(): string
    {
        return 'string';
    }

    public function apply(int|string $raw, CreateNotificationTaskDraft $draft, ValidationFailedException $failure): void
    {
        $channel = $draft->channel;

        if (!$channel instanceof NotificationChannel) {
            // Канал не распознан - ChannelRule уже записал ошибку, по recipient не дублируем
            return;
        }

        $value = trim($raw);

        try {
            $draft->recipient = match ($channel) {
                NotificationChannel::Email => new Email($value),
                NotificationChannel::Sms => new Phone($value),
            };
        } catch (EmailException | PhoneException $e) {
            $failure->add(new FieldValidationException($this->field(), $e->getMessage()));
        }
    }
}
