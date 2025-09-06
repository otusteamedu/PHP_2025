<?php

namespace App\Middlewares;

use App\Helpers\HttpHelper;
use App\Services\JwtService;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Psr7\Response as SlimResponse;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class JwtMiddleware implements MiddlewareInterface
{
    public function __construct(private JwtService $service) {}

    public function process(Request $request, RequestHandler $handler): Response
    {
        $authHeader = $request->getHeaderLine('Authorization');

         if (empty($authHeader)) {
            return HttpHelper::send401Error(new SlimResponse());
        }

        $userid = $this->service->verifyJwt($authHeader);

        if ($userid === 0) {
            return HttpHelper::send401Error(new SlimResponse());
        }

        $request = $request->withAttribute('userid', $userid);

        return $handler->handle($request);
    }
}