<?php declare(strict_types=1);

namespace App\Application\Service\ReportGenerator;

readonly class ReportGeneratorRequest
{
    public function __construct(
        public iterable $newsList
    )
    {

    }
}
