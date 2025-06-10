<?php
declare(strict_types=1);

namespace Application\Gateway;

interface LicenseGatewayInterface
{
    public function sendLead(LicenseGatewayRequest $request): LicenseGatewayResponse;
}