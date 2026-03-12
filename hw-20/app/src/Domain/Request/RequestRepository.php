<?php

declare(strict_types=1);

namespace App\Domain\Request;

interface RequestRepository
{
    public function create(string $payload, string $status): int;

    public function updateStatus(int $id, string $status): void;

    public function getRequest(int $id): ?array;
}
