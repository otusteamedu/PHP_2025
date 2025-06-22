<?php

declare(strict_types=1);

namespace App\Shared\Application\Service;

interface FileHelperInterface
{
    public function readStream(string $fileName): mixed;

    public function isExist(string $filename): bool;

    public function save(string $content, string $fileName): void;

    public function getFileMimeType(string $filename): string;
}
