<?php

declare(strict_types=1);

namespace App\Controller\Http\Api\OrderNotification\Rule;

use App\Controller\Http\Api\OrderNotification\DataObject\CreateNotificationTaskDraft;
use App\Controller\Http\Api\Shared\Validation\ValidationFailedException;

interface ValidationRuleInterface
{
    /**
     * Имя поля запроса, за которое отвечает правило
     */
    public function field(): string;

    /**
     * Ожидаемый тип сырого значения: 'int' или 'string'. Движок проверяет его до apply().
     */
    public function expectedType(): string;

    /**
     * Семантика поля: сырое значение гарантированно присутствует и соответствует expectedType().
     */
    public function apply(int|string $raw, CreateNotificationTaskDraft $draft, ValidationFailedException $failure): void;
}
