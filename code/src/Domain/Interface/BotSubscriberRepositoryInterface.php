<?php

declare(strict_types=1);

namespace MkdBot\Domain\Interface;

use MkdBot\Domain\Entity\BotSubscriber;

/**
 * Интерфейс репозитория подписчиков бота
 */
interface BotSubscriberRepositoryInterface
{
    /**
     * Находит всех активных подписчиков
     *
     * @return BotSubscriber[]
     */
    public function findActive(): array;

    /**
     * Находит подписчика по ID пользователя
     */
    public function findByUserId(int $userId): ?BotSubscriber;

    /**
     * Сохраняет подписчика в БД
     */
    public function save(BotSubscriber $subscriber): BotSubscriber;

    /**
     * Помечает подписчика как неактивного (bot_stopped)
     */
    public function markAsInactive(int $userId): void;

    /**
     * Помечает подписчика как активного (bot_started повторно)
     */
    public function markAsActive(int $userId): void;
}
