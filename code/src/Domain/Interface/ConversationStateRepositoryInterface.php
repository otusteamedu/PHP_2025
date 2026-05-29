<?php

declare(strict_types=1);

namespace MkdBot\Domain\Interface;

use MkdBot\Domain\Entity\ConversationState;

/**
 * Интерфейс репозитория состояний диалога
 */
interface ConversationStateRepositoryInterface
{
    /**
     * Находит состояние диалога по ID пользователя
     */
    public function findByUserId(int $userId): ?ConversationState;

    /**
     * Сохраняет состояние диалога (upsert — одна активная сессия на пользователя)
     */
    public function save(ConversationState $state): ConversationState;

    /**
     * Удаляет состояние диалога по ID пользователя
     */
    public function deleteByUserId(int $userId): bool;
}
