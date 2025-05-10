<?php

namespace App\Application\Port;

interface ReportServiceInterface
{
    public function dumpReport(string $htmlReport, string $fileName):string;
}