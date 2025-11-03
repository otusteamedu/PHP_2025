<?php

namespace App\Core\UserInterface\UserReport;

use App\Core\Application\UserReportHandler;
use App\Core\Application\UserReportQuery;

class UserReportController
{
    public function generateReport(Request $request): void
    {
        $query = new UserReportQuery(
            userId: $request->userId,
            interval: $request->interval,
            cardId: $request->cardId,
            email: $request->email,
        );

        $handler = new UserReportHandler;
        $handler($query);
    }

}