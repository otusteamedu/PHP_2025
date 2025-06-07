<?php

declare(strict_types=1);

namespace App\Application;

class App
{
    private readonly Request $request;
    private readonly Router $router;

    public function __construct()
    {
        $this->request = new Request();
        $this->router = new Router();
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
