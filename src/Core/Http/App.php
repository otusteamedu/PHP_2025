<?php

declare(strict_types=1);

namespace App\Core\Http;

use App\Core\Http\Message\Request;
use App\Core\Http\Message\Response;
use App\Core\Http\Routing\Router;

class App
{
    public function __construct(
        private readonly Request $request,
        private readonly Router $router,
    ) {
    }

    public function run(): Response
    {
        $requestPath = $this->request->getRequestPath();
        $requestMethod = $this->request->getRequestMethod();

        $response = $this->router->dispatch($requestPath, $requestMethod);
        foreach ($response->getHeaders() as $header) {
            header($header);
        }
        http_response_code($response->getHttpCode());

        return $response;
    }
}
