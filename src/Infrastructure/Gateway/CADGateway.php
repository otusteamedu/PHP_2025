<?php
declare(strict_types=1);

namespace Infrastructure\Gateway;

use Application\Gateway\LicenseGatewayRequest;
use Application\Gateway\LicenseGatewayResponse;

class CADGateway
{
    public function sendLicense(LicenseGatewayRequest $request): LicenseGatewayResponse
    {
        sleep(2);
        $serialNumber = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'),1, 12);
        if (strlen($serialNumber) === 12) {
            throw new \Exception('Error CADGateway sendLicense');
        }

        return new LicenseGatewayResponse($serialNumber);
    }
}