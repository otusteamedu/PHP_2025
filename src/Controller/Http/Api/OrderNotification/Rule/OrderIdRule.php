<?php

declare(strict_types=1);

namespace App\Controller\Http\Api\OrderNotification\Rule;

use App\Controller\Http\Api\OrderNotification\DataObject\CreateNotificationTaskDraft;
use App\Controller\Http\Api\Shared\Validation\ValidationFailedException;
use App\Core\Http\Exception\FieldValidationException;
use App\Domain\OrderNotification\Exception\Input\OrderIdException;
use App\Domain\OrderNotification\ValueObject\Order\OrderId;

class OrderIdRule implements ValidationRuleInterface
{
    public function field(): string
    {
        return 'orderId';
    }

    public function expectedType(): string
    {
        return 'int';
    }

    public function apply(int|string $raw, CreateNotificationTaskDraft $draft, ValidationFailedException $failure): void
    {
        try {
            $draft->orderId = OrderId::fromInt($raw);
        } catch (OrderIdException $e) {
            $failure->add(new FieldValidationException($this->field(), $e->getMessage()));
        }
    }
}
