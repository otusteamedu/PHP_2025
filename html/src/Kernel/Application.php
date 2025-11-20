<?php

declare(strict_types=1);

namespace Otus\Kernel;

use Otus\Handlers\GetHandler;
use Otus\Handlers\PostHandler;
use Otus\Kernel\Http\Request;
use Otus\Kernel\Http\Response;

readonly class Application
{
    /**
     * @param Request $request
     */
    public function __construct(
        public Request $request,
    ) {
        session_start();
    }

    /**
     * @return Response
     */
    public function run(): Response
    {
        return match ($this->request->server->get('REQUEST_METHOD')) {
            'GET' => new GetHandler()->handle($this->request),
            'POST' => new PostHandler()->handle($this->request),
        };
    }
}
