<?php

namespace App\Application\Gateway;

interface ReportGatewayInterface
{
    public function saveReport(ReportGatewayRequest $newsList): ?ReportGatewayResponse;
}
