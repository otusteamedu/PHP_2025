<?php

declare(strict_types=1);

namespace App\Application\Interface;

use App\Domain\Entity\Request;

interface RequestRepositoryInterface
{
    /**
     * Сохраняет запрос
     */
    public function save(Request $request): void;

    /**
     * Находит запрос по ID
     */
    public function findById(string $id): ?Request;

    /**
     * Возвращает все запросы
     */
    public function findAll(): array;
}
