<?php

declare(strict_types=1);

namespace MkdBot\Domain\Interface;

use MkdBot\Domain\Entity\News;

/**
 * Интерфейс репозитория новостей
 */
interface NewsRepositoryInterface
{
    /**
     * Находит новость по ID
     */
    public function findById(int $id): ?News;

    /**
     * Находит все новости со статусом pending
     *
     * @return News[]
     */
    public function findPending(): array;

    /**
     * Находит все новости со статусом delivering
     *
     * @return News[]
     */
    public function findDelivering(): array;

    /**
     * Сохраняет новость в БД
     */
    public function save(News $news): News;

    /**
     * Помечает новость как delivering
     */
    public function markAsDelivering(int $newsId): void;

    /**
     * Помечает новость как delivered
     */
    public function markAsDelivered(int $newsId): void;
}
