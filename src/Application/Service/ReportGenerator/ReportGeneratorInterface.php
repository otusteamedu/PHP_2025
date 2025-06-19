<?php declare(strict_types=1);

namespace App\Application\Service\ReportGenerator;

interface ReportGeneratorInterface
{
    public function generate(ReportGeneratorRequest $request): ReportGeneratorResponse;
}
