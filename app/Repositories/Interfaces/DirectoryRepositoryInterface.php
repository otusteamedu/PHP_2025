<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

interface DirectoryRepositoryInterface
{
    public function getDirectory(int $id): ?array;
    public function saveDirectory(string $directoryPath): bool;
};