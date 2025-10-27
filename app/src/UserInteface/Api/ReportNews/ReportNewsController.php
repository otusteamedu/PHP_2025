<?php

declare(strict_types=1);

namespace App\UserInteface\Api\ReportNews;

use App\Application\ReportNews\ReportNewsHandler;
use App\Application\ReportNews\ReportNewsQuery;
use App\UserInteface\Api\ReportNews\Request\Request as ReportNewsRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
class ReportNewsController
{
    #[Route('/v1/report/news', name: 'report_news', methods: Request::METHOD_POST)]
    public function report(
        #[MapRequestPayload] ReportNewsRequest $request,
        ReportNewsHandler $handler,
    ): Response
    {
        $query = new ReportNewsQuery(ids: $request->ids);
        $output = $handler($query);

        return new Response($output->html, 200, ['Content-Type' => 'text/html']);
    }
}
