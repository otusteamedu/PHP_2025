<?php

namespace App\Loader;

class BookLoader
{
    private string $filename;

    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    /**
     * возвращает массив строк (bulk-команд) для Elasticsearch
     */
    public function loadBulkLines(): array
    {
        $lines = file($this->filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $body = [];

        foreach ($lines as $line) {
            $decoded = json_decode($line, true);
            if ($decoded === null) {
                throw new \RuntimeException("Ошибка JSON в строке: $line");
            }
            $body[] = $decoded;
        }

        return $body;
    }
}
