<?php
declare(strict_types=1);

namespace Dinargab\Homework20\Infrastructure\Http;

use Dinargab\Homework20\Application\Statement\GetStatement\GetStatementRequest;
use Dinargab\Homework20\Application\Statement\GetStatement\GetStatementUseCase;
use Dinargab\Homework20\Application\Statement\RequestStatement\RequestStatementRequest;
use Dinargab\Homework20\Application\Statement\RequestStatement\RequestStatementUseCase;
use Dinargab\Homework20\Domain\Exception\EntityNotFoundException;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class StatementController
{
    public function __construct(
        private readonly RequestStatementUseCase $useCase,
        private readonly GetStatementUseCase $getStatementUseCase,
    )
    {

    }

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

    public function getStatement(Request $request, Response $response, int $id): Response
    {
        $getStatementRequest = new GetStatementRequest($id);
        try {
            $statementResponse = ($this->getStatementUseCase)($getStatementRequest);
        } catch (EntityNotFoundException $exception) {
            return $response->withStatus(404);
        }
        $response->getBody()->write(json_encode([
            'statementId' => $statementResponse->statementId,
            'dateFrom' => $statementResponse->dateFrom,
            'dateTo' => $statementResponse->dateTo,
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}