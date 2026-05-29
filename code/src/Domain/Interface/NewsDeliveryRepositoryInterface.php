<?php

declare(strict_types=1);

namespace MkdBot\Domain\Interface;

use MkdBot\Domain\Entity\NewsDelivery;

/**
 * Интерфейс репозитория доставок новостей
 */
interface NewsDeliveryRepositoryInterface
{
    /**
     * Находит все доставки со статусом pending для указанной новости
     *
     * @return NewsDelivery[]
     */
    public function findPendingByNewsId(int $newsId): array;

    /**
     * Находит доставку по ID новости и ID пользователя
     */
    public function findByNewsIdAndUserId(int $newsId, int $userId): ?NewsDelivery;

    /**
     * Сохраняет доставку в БД
     */
    public function save(NewsDelivery $delivery): NewsDelivery;

    /**
     * Помечает доставку как отправленную
     */
    public function markAsSent(int $newsId, int $userId): void;

    /**
     * Помечает доставку как неудачную
     */
    public function markAsFailed(int $newsId, int $userId): void;

    /**
     * Возвращает массив user_id, для которых уже существует доставка по указанной новости
     *
     * @return int[] Массив user_id, для которых уже существует доставка по указанной новости
     */
    public function findExistingDeliveryUserIds(int $newsId): array;
}
