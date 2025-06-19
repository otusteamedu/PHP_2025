<?php declare(strict_types=1);

namespace App\Application\UseCase\ReportNews;

readonly class ReportNewsResponse
{
    public function __construct(
        public string $filepath,
    )
    {
    }

    public function getFileName(): string {
        return basename($this->filepath);
    }
}
