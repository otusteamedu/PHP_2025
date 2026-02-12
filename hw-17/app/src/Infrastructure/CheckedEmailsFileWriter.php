<?php

declare(strict_types=1);

namespace App\Infrastructure;

use RuntimeException;

readonly class CheckedEmailsFileWriter
{
    public function __construct(private string $filePath)
    {
    }

    public function writeEmails(array $emails): void
    {
        $data = implode(PHP_EOL, $emails) . PHP_EOL;

        if (file_put_contents($this->filePath, $data) === false) {
            throw new RuntimeException("Unable to write to file: $this->filePath");
        }
    }
}
