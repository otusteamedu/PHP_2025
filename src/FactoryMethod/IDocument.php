<?php

declare(strict_types=1);

namespace App\FactoryMethod;

interface IDocument
{
    public function save(string $content): void;
    public function load(string $path): string;
    public function open(string $path): void;
    public function close(): void;
}
