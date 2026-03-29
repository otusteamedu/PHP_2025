<?php

declare(strict_types=1);

namespace App\Domain\Enums;

enum OrderStatus: string
{
    case CREATED = 'created';
    case PREPARING = 'preparing';
    case COOKING = 'cooking';
    case QUALITY_CHECK = 'quality_check';
    case READY = 'ready';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';
    case DISPOSED = 'disposed';

    public function getDescription(): string
    {
        return match ($this) {
            self::CREATED => 'Заказ создан',
            self::PREPARING => 'Подготовка ингредиентов',
            self::COOKING => 'Готовится на кухне',
            self::QUALITY_CHECK => 'Проверка качества',
            self::READY => 'Готов к выдаче',
            self::DELIVERED => 'Выдан клиенту',
            self::CANCELLED => 'Заказ отменён',
            self::DISPOSED => 'Утилизирован (не прошёл проверку)',
        };
    }

    public function getNextStatus(): ?self
    {
        return match ($this) {
            self::CREATED => self::PREPARING,
            self::PREPARING => self::COOKING,
            self::COOKING => self::QUALITY_CHECK,
            self::QUALITY_CHECK => self::READY,
            self::READY => self::DELIVERED,
            self::DELIVERED, self::CANCELLED, self::DISPOSED => null,
        };
    }

    public function canTransitionTo(self $newStatus): bool
    {
        if ($this === self::DELIVERED || $this === self::CANCELLED) {
            return false;
        }

        if ($newStatus === self::CANCELLED) {
            return in_array($this, [self::CREATED, self::PREPARING, self::COOKING]);
        }

        if ($this === self::QUALITY_CHECK && $newStatus === self::PREPARING) {
            return true;
        }

        return $this->getNextStatus() === $newStatus;
    }
}
