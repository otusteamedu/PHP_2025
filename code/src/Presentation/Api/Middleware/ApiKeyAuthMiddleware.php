<?php

declare(strict_types=1);

namespace Api\Presentation\Api\Middleware;

use Api\Presentation\Api\Helpers\Json;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class ApiKeyAuthMiddleware implements MiddlewareInterface
{
    private const HEADER_NAME = 'X-API-Key';

    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private array $validKeys,
    ) {
    }

    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        $apiKey = $request->getHeaderLine(self::HEADER_NAME);

        if (empty($apiKey) || !in_array($apiKey, $this->validKeys, true)) {
            $response = $this->responseFactory->createResponse();
            return Json::response($response, ['error' => 'Неверный или отсутствующий API ключ'], 401);
        }

        return $handler->handle($request);
    }
}
