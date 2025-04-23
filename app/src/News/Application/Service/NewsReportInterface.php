<?php
declare(strict_types=1);


namespace App\News\Application\Service;

interface NewsReportInterface
{
    public function generate(string ...$newsId): string;

}