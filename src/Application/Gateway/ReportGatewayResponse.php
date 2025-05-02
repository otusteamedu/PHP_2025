<?php

namespace App\Application\Gateway;

class ReportGatewayResponse
{
    public function __construct(
        public readonly string $reportPath
    ){}
}