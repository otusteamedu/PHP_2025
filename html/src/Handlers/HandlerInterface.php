<?php

declare(strict_types=1);

namespace Otus\Handlers;

use Otus\Kernel\Http\Request;
use Otus\Kernel\Http\Response;

interface HandlerInterface
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function handle(Request $request): Response;
}
