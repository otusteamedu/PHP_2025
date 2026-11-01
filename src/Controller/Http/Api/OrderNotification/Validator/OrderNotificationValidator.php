<?php

declare(strict_types=1);

namespace App\Controller\Http\Api\OrderNotification\Validator;

use App\Controller\Http\Api\OrderNotification\DataObject\CreateNotificationTaskDraft;
use App\Controller\Http\Api\OrderNotification\Rule\ChannelRule;
use App\Controller\Http\Api\OrderNotification\Rule\OrderEventRule;
use App\Controller\Http\Api\OrderNotification\Rule\OrderIdRule;
use App\Controller\Http\Api\OrderNotification\Rule\RecipientRule;
use App\Controller\Http\Api\OrderNotification\Rule\ValidationRuleInterface;
use App\Controller\Http\Api\Shared\Validation\ValidationFailedException;
use App\Core\Http\Exception\FieldValidationException;
use App\Domain\OrderNotification\Command\CreateNotificationTaskCommand;

class OrderNotificationValidator
{
    /** @var list<ValidationRuleInterface> */
    private readonly array $rules;

    public function __construct(
        OrderIdRule $orderIdRule,
        OrderEventRule $orderEventRule,
        ChannelRule $channelRule,
        RecipientRule $recipientRule,
    ) {
        $this->rules = [
            $orderIdRule,
            $orderEventRule,
            $channelRule,
            $recipientRule,
        ];
    }

    /**
     * @throws ValidationFailedException
     */
    public function validate(array $payload): CreateNotificationTaskCommand
    {
        $failure = new ValidationFailedException('Ошибки валидации.');

        $this->checkUnknownFields($payload, $failure);

        $draft = new CreateNotificationTaskDraft();

        foreach ($this->rules as $rule) {
            $this->runRule($rule, $payload, $draft, $failure);
        }

        $failure->throwIfAny();

        return $draft->toCommand();
    }

    private function checkUnknownFields(array $payload, ValidationFailedException $failure): void
    {
        $allowed = array_map(static fn(ValidationRuleInterface $rule) => $rule->field(), $this->rules);

        foreach (array_keys($payload) as $field) {
            if (!in_array($field, $allowed, true)) {
                $failure->add(new FieldValidationException($field, 'Передано неизвестное поле.'));
            }
        }
    }

    private function runRule(
        ValidationRuleInterface $rule,
        array $payload,
        CreateNotificationTaskDraft $draft,
        ValidationFailedException $failure,
    ): void {
        $field = $rule->field();

        if (!array_key_exists($field, $payload) || $payload[$field] === null) {
            $failure->add(new FieldValidationException($field, 'Поле обязательно для заполнения.'));
            return;
        }

        $raw = $payload[$field];

        $expected = $rule->expectedType();
        if (!$this->isExpectedType($raw, $expected)) {
            $failure->add(new FieldValidationException(
                $field,
                sprintf('Неверный тип поля. Ожидается %s, получено: %s', $expected, $this->jsonType($raw))),
            );
            return;
        }

        $rule->apply($raw, $draft, $failure);
    }

    private function isExpectedType(mixed $raw, string $expectedType): bool
    {
        return match ($expectedType) {
            'int' => is_int($raw),
            'string' => is_string($raw),
            default => throw new \LogicException(sprintf('Неизвестный ожидаемый тип: %s.', $expectedType)),
        };
    }

    private function jsonType(mixed $value): string
    {
        return match (true) {
            is_int($value) => 'int',
            is_float($value) => 'float',
            is_string($value) => 'string',
            is_bool($value) => 'boolean',
            is_array($value) => 'array',
            default => 'unknown',
        };
    }
}
