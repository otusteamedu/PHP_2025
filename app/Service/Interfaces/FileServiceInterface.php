<?php

namespace App\Service\Interfaces;

use App\Models\FileModel;

interface FileServiceInterface
{
    public function getList();

    public function getFile(int $id): FileModel;

    public function addNewFile(string $name, string $tmp, ?string $directory): bool;

    public function checkDirectoryExists(string $directory): bool;

    public function addDirectory(string $directoryName): bool;

    public function getDirectoryInfo(int $id): array;
}