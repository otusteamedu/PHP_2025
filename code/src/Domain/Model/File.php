<?php

declare(strict_types=1);

namespace App\Domain\Model;

class File
{
    public function __construct(
        private string $path,
        private int $size,
        private string $type,
        private ?string $content = null
    ) {
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }
}
