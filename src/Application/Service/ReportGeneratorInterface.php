<?php declare(strict_types=1);

namespace App\Application\Service;

interface ReportGeneratorInterface
{
    public function generate(iterable $newsList): string;
    public function saveToFile(string $html, string $filename): void;
}
