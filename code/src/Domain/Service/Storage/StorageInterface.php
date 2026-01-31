<?php

declare(strict_types=1);

namespace App\Domain\Service\Storage;

use App\Domain\Model\File;

interface StorageInterface
{
    /**
     * Читает файл по указанному пути и возвращает его в виде модели File.
     *
     * @param string $path
     * @return File|null
     */
    public function read(string $path): ?File;

    /**
     * Записывает содержимое в файл.
     *
     * @param string $path
     * @param string $content
     * @return bool - true в случае успеха
     */
    public function write(string $path, string $content): bool;

    /**
     * Проверяет, существует ли файл по указанному пути.
     *
     * @param string $path
     * @return bool
     */
    public function exists(string $path): bool;

    /**
     * Удаляет файл.
     *
     * @param string $path
     * @return bool
     */
    public function delete(string $path): bool;
}
