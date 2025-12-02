<?php
declare(strict_types=1);

namespace Dinargab\Homework20\Infrastructure\Http;


use Dinargab\Homework20\Application\Job\GetJobs\GetAllJobsUseCase;
use Dinargab\Homework20\Application\Job\GetJobStatus\GetJobStatusRequest;
use Dinargab\Homework20\Application\Job\GetJobStatus\GetJobStatusUseCase;
use Dinargab\Homework20\Domain\Exception\EntityNotFoundException;
use FastRoute\RouteParser;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteContext;

class JobsController
{

    public function __construct(
        private GetAllJobsUseCase   $getAllJobsUseCase,
        private GetJobStatusUseCase $getJobStatusUseCase,
    )
    {
        // Get the RouteParser from the RouteContext

    }

    public function index(Request $request, Response $response): Response
    {
        $jobs = ($this->getAllJobsUseCase)();
        $response->getBody()->write(json_encode(["jobs" => $jobs]));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function getJobStatus(Request $request, Response $response, int $id): Response
    {
        $requestJobStatus = new GetJobStatusRequest($id);
        try {
            $useCaseResponse = ($this->getJobStatusUseCase)($requestJobStatus);
        } catch (EntityNotFoundException $exception) {
            return $response->withStatus(404);
        }
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $statementUrl = $routeParser->urlFor('statement', ['id' => $useCaseResponse->getStatementId()]);

        $response->getBody()->write(json_encode([
            'jobId' => $useCaseResponse->getJobId(),
            'status' => $useCaseResponse->getStatus(),
            'statementUrl' => $statementUrl
        ]));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

}