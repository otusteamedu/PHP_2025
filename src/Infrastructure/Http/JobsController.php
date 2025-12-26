<?php
declare(strict_types=1);

namespace Dinargab\Homework20\Infrastructure\Http;


use Dinargab\Homework20\Application\Job\GetJobs\GetAllJobsUseCase;
use Dinargab\Homework20\Application\Job\GetJobStatus\GetJobStatusRequest;
use Dinargab\Homework20\Application\Job\GetJobStatus\GetJobStatusUseCase;
use Dinargab\Homework20\Domain\Exception\EntityNotFoundException;
use OpenApi\Attributes as OA;
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

    }

    #[OA\Get(
        path: "/api/v1/jobs",
        operationId: "getAllJobs",
        tags: ["Jobs"],
        summary: "Get all jobs",
        description: "Retrieves a list of all jobs",
        responses: [
            new OA\Response(
                response: 200,
                description: "Successful operation",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "jobs",
                            type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "id", type: "integer", example: 1),
                                    new OA\Property(property: "status", type: "string", example: "completed"),
                                ],
                                type: "object"
                            )
                        )
                    ],
                    type: "object"
                )
            ),
        ]
    )]
    public function index(Request $request, Response $response): Response
    {
        $jobs = ($this->getAllJobsUseCase)();
        $response->getBody()->write(json_encode(["jobs" => $jobs]));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    #[OA\Get(
        path: "/api/v1/jobs/{id}",
        operationId: "getJobStatus",
        tags: ["Jobs"],
        summary: "Get job status",
        description: "Retrieves the status of a specific job by ID",
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "Job ID",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer", minimum: 1)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Successful operation",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "jobId", type: "integer", example: 1),
                        new OA\Property(
                            property: "status",
                            type: "string",
                            enum: ["new", "active", "completed"],
                            example: "completed"
                        ),
                        new OA\Property(
                            property: "statementUrl",
                            type: "string",
                            example: "/api/v1/statement/123",
                            nullable: true
                        )
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 404,
                description: "Job not found",
            ),
        ]
    )]
    public function getJobStatus(Request $request, Response $response, int $id): Response
    {
        $requestJobStatus = new GetJobStatusRequest($id);
        try {
            $useCaseResponse = ($this->getJobStatusUseCase)($requestJobStatus);
        } catch (EntityNotFoundException $exception) {
            return $response->withStatus(404);
        }
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $returnArray = [
            'jobId' => $useCaseResponse->getJobId(),
            'status' => $useCaseResponse->getStatus(),
        ];
        if ($useCaseResponse->getStatementId()) {
            $returnArray["statementUrl"] = $routeParser->urlFor('statement', ['id' => $useCaseResponse->getStatementId()]);
        }
        $response->getBody()->write(json_encode($returnArray));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

}