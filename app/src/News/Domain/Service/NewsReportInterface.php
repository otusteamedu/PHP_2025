<?php

declare(strict_types=1);

namespace App\News\Domain\Service;

interface NewsReportInterface
{
    public function generate(string ...$newsId): string;

}
