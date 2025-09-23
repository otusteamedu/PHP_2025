<?php
declare(strict_types=1);

namespace App\EventSystem\ApiActions;

use App\Application\Http\Request;
use App\Application\Http\Response;
use App\Application\Router\RouteAction;
use App\EventSystem\EventSystemRepository;

final readonly class ClearEvents implements RouteAction
{
    public function __construct(private EventSystemRepository $repository)
    {
    }

    public function handle(Request $request): Response
    {
        $this->repository->clearAll();
        return Response::json(['message' => 'Events cleared']);
    }
}
