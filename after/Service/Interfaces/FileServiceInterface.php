<?php

namespace Service\Interfaces;

use Models\FileModel;

interface FileServiceInterface
{
    public function getList(): array;
    public function getFile(int $id): FileModel;
    public function addNewFile(string $name, string $tmp): bool;
    public function addDirectory(string $directoryName): bool;
    public function getDirectoryInfo(int $id): array;
}