<?php

namespace App\Application\Gateway;

class ReportGatewayRequest
{
    public function __construct(
        public readonly string $html
    ){}
}