<?php

declare(strict_types=1);

namespace App\Infrastructure;

use RuntimeException;

readonly class EmailsFileReader
{
    public function __construct(private string $filePath)
    {

    }

    public function readEmails(): array
    {
        if (!is_file($this->filePath)) {
            throw new RuntimeException("File not found: $this->filePath");
        }

        $lines = file($this->filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if ($lines === false) {
            throw new RuntimeException("Unable to read file: $this->filePath");
        }

        return $lines;
    }
}
