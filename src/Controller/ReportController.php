<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\ReportService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ReportController
{
    public function __construct(private ReportService $service) {}

    public function create(Request $request, Response $response): Response
    {
        $id = $this->service->enqueueReport();
        $payload = ['id' => $id];
        $response->getBody()->write(json_encode($payload));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function status(Request $request, Response $response, array $args): Response
    {
        $id = (int)$args['id'];
        $status = $this->service->getReportStatus($id);

        if ($status === null) {
            $response->getBody()->write(json_encode(['error' => 'Report not found']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $response->getBody()->write(json_encode(['id' => $id, 'status' => $status]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}
