<?php

declare(strict_types=1);

namespace App\News\Application\Service;

interface NewsReportGeneratorInterface
{
    public function generate(array $newsDTOs): string;
}
