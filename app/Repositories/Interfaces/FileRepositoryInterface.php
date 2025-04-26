<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\FileModel;

interface FileRepositoryInterface
{
    public function getFilesList(): array;
    public function getFileById(int $id): ?FileModel;
    public function saveFile(string $fileName): bool;
}