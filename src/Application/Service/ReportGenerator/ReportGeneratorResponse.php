<?php declare(strict_types=1);

namespace App\Application\Service\ReportGenerator;

readonly class ReportGeneratorResponse
{
    public function __construct(
        public string $path
    )
    {

    }
}
