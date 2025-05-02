<?php

namespace App\Infrastructure\Gateway;

use App\Application\Gateway\ReportGatewayInterface;
use App\Application\Gateway\ReportGatewayRequest;
use App\Application\Gateway\ReportGatewayResponse;

class ReportGateway implements ReportGatewayInterface
{
    const SAVE_PATH = "/reports";

    public function saveReport(ReportGatewayRequest $reportGatewayRequest): ?ReportGatewayResponse
    {
        $filePath = self::SAVE_PATH.'/report'.(new \DateTime())->format('Ymd-His').'.html';
        file_put_contents(PUBLIC_DIR.$filePath, $reportGatewayRequest->html);

        return new ReportGatewayResponse($filePath);
    }
}