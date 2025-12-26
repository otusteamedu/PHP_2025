<?php
declare(strict_types=1);

namespace Dinargab\Homework20\Infrastructure\Http;

use Dinargab\Homework20\Application\Statement\GetStatement\GetStatementRequest;
use Dinargab\Homework20\Application\Statement\GetStatement\GetStatementUseCase;
use Dinargab\Homework20\Application\Statement\RequestStatement\RequestStatementRequest;
use Dinargab\Homework20\Application\Statement\RequestStatement\RequestStatementUseCase;
use Dinargab\Homework20\Domain\Exception\EntityNotFoundException;
use InvalidArgumentException;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class StatementController
{
    public function __construct(
        private readonly RequestStatementUseCase $useCase,
        private readonly GetStatementUseCase     $getStatementUseCase,
    )
    {

    }


    #[OA\Post(
        path: "/api/v1/statement",
        operationId: "requestStatement",
        tags: ["Statements"],
        summary: "Request a new statement",
        description: "Creates a new statement request and adds it to the processing queue",
        requestBody: new OA\RequestBody(
            description: "Statement date range",
            required: true,
            content: new OA\JsonContent(
                required: ["dateFrom", "dateTo"],
                properties: [
                    new OA\Property(
                        property: "dateFrom",
                        description: "Start date (YYYY-MM-DD)",
                        type: "string",
                        format: "date",
                        example: "2024-01-01"
                    ),
                    new OA\Property(
                        property: "dateTo",
                        description: "End date (YYYY-MM-DD)",
                        type: "string",
                        format: "date",
                        example: "2024-12-31"
                    )
                ],
                type: "object"
            )
        ),
        responses: [
            new OA\Response(
                response: 202,
                description: "Request accepted and queued for processing",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Request added to queue, await processing"),
                        new OA\Property(property: "jobId", type: "integer", example: 123)
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 400,
                description: "Bad request",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Missing required parameters: dateFrom dateTo"),
                    ],
                    type: "object"
                )
            ),
        ]
    )]
    public function store(Request $request, Response $response): Response
    {
        $params = $request->getParsedBody();
        if (!isset($params["dateFrom"]) || !isset($params["dateTo"])) {
            $response->getBody()->write(json_encode([
                'message' => 'Missing required parameters: ' . (isset($params["dateFrom"]) ? '' : 'dateFrom') . ' ' . (isset($params["dateTo"]) ? '' : 'dateTo'),
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        $requestStatementRequest = new RequestStatementRequest($params["dateFrom"], $params["dateTo"]);

        try {
            $requestStatementResponse = ($this->useCase)($requestStatementRequest);
        } catch (InvalidArgumentException $exception) {
            $response->getBody()->write(json_encode([
                'message' => $exception->getMessage(),
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        $response->getBody()->write(json_encode([
            'message' => 'Request added to queue, await processing',
            'jobId' => $requestStatementResponse->jobId,
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(202);
    }

    #[OA\Get(
        path: "/api/v1/statement/{id}",
        operationId: "getStatement",
        tags: ["Statements"],
        summary: "Get statement by ID",
        description: "Retrieves a statement by its ID",
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "Statement ID",
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
                        new OA\Property(property: "statementId", type: "integer", example: 123),
                        new OA\Property(property: "dateFrom", type: "string", format: "date", example: "2024-01-01"),
                        new OA\Property(property: "dateTo", type: "string", format: "date", example: "2024-12-31")
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 404,
                description: "Statement not found",
            ),
        ]
    )]
    public function getStatement(Request $request, Response $response, int $id): Response
    {
        $getStatementRequest = new GetStatementRequest($id);
        try {
            $statementResponse = ($this->getStatementUseCase)($getStatementRequest);
        } catch (EntityNotFoundException $exception) {
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
        $response->getBody()->write(json_encode([
            'statementId' => $statementResponse->statementId,
            'dateFrom' => $statementResponse->dateFrom,
            'dateTo' => $statementResponse->dateTo,
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}