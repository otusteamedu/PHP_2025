<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\Storage;

use App\Domain\Model\File;
use App\Domain\Service\Storage\StorageInterface;

class LocalFileSystemStorage implements StorageInterface
{
    public function read(string $path): ?File
    {
        if (!$this->exists($path)) {
            return null;
        }

        $content = file_get_contents($path);
        if ($content === false) {
            // Можно добавить логирование ошибки
            return null;
        }

        $file = new File(
            path: $path,
            size: filesize($path),
            type: filetype($path),
            content: $content
        );

        return $file;
    }

    public function write(string $path, string $content): bool
    {
        return file_put_contents($path, $content) !== false;
    }

    public function exists(string $path): bool
    {
        return file_exists($path);
    }

    public function delete(string $path): bool
    {
        if (!$this->exists($path)) {
            return true; // Файла и так нет
        }
        return unlink($path);
    }
}
