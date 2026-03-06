<?php
declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Presentation\Http\Request;
use App\Presentation\Http\Response;
use App\Presentation\Http\Router;

class Application
{
    public function __construct(
        private readonly Router $router
    ) {}

    public function run(): Response
    {
        $request = Request::fromGlobals();

        return $this->router->dispatch($request) ?? Response::html('Not Found', 404);
    }
}
