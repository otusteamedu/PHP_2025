<?php

declare(strict_types=1);

namespace App\FactoryMethod;

final class WordDocument implements IDocument
{
    private ?string $filePath = null;

    public function save(string $content): void
    {
        if ($this->filePath === null) {
            throw new \RuntimeException('File path not set. Call open() first.');
        }
        file_put_contents($this->filePath, $content);
    }

    public function load(string $path): string
    {
        return file_get_contents($path);
    }

    public function open(string $path): void
    {
        $this->filePath = $path;
    }

    public function close(): void
    {
        $this->filePath = null;
    }
}
