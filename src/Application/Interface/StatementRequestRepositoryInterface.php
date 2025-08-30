<?php

declare(strict_types=1);

namespace App\Application\Interface;

use App\Domain\Entity\BankStatementRequest;

interface StatementRequestRepositoryInterface
{
    /**
     * Сохраняет запрос на банковскую выписку
     */
    public function save(BankStatementRequest $request): void;

    /**
     * Находит запрос по id
     */
    public function findById(string $id): ?BankStatementRequest;

    /**
     * Возвращает все запросы на банковские выписки
     */
    public function findAll(): array;

    /**
     * Возвращает все ожидающие обработки запросы
     */
    public function findPending(): array;
}
