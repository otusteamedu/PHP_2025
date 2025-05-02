<?php

namespace App\Application\Gateway;

interface InternetGatewayInterface
{
    public function getTitle(InternetGatewayRequest $internetGatewayRequest): InternetGatewayResponse;
}