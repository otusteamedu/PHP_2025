<?php

namespace App\Application\Gateway;

interface InternetGatewayInterface
{
//    public function sendLead(BankGatewayRequest $request): BankGatewayResponse;
    public function getTitle(InternetGatewayRequest $internetGatewayRequest): InternetGatewayResponse;
}