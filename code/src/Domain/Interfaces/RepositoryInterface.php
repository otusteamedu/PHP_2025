<?php

declare(strict_types=1);

namespace Api\Domain\Interfaces;

use Api\Domain\Entities\Request;

interface RepositoryInterface
{
    public function add(Request $request): Request;

    public function getById(int $id): ?Request;

    public function updateStatus(int $id, Request $request): void;
}
