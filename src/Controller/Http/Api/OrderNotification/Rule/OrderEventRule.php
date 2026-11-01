<?php

declare(strict_types=1);

namespace App\Controller\Http\Api\OrderNotification\Rule;

use App\Controller\Http\Api\OrderNotification\DataObject\CreateNotificationTaskDraft;
use App\Controller\Http\Api\Shared\Validation\ValidationFailedException;
use App\Core\Http\Exception\FieldValidationException;
use App\Domain\OrderNotification\ValueObject\Order\OrderEvent;

class OrderEventRule implements ValidationRuleInterface
{
    public function field(): string
    {
        return 'orderEvent';
    }

    public function expectedType(): string
    {
        return 'string';
    }

    public function apply(int|string $raw, CreateNotificationTaskDraft $draft, ValidationFailedException $failure): void
    {
        $value = strtolower(trim($raw));

        $event = OrderEvent::tryFrom($value);

        if ($event === null) {
            $allowed = implode(', ', OrderEvent::values());
            $failure->add(new FieldValidationException(
                $this->field(),
                sprintf('Недопустимое событие заказа: %s. Допустимые события: %s', $raw, $allowed),
            ));
            return;
        }

        $draft->orderEvent = $event;
    }
}
