<?php

namespace App\Application\Gateway;

class InternetGatewayRequest
{
    public function __construct(
        public readonly string $url,
    )
    {
    }
}