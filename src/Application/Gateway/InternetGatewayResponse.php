<?php

namespace App\Application\Gateway;

class InternetGatewayResponse
{
    public function __construct(
        public readonly string $title,
    )
    {
    }
}