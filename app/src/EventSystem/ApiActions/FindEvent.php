<?php
declare(strict_types = 1);

namespace App\EventSystem\ApiActions;

use App\Application\Http\Request;
use App\Application\Http\Response;
use App\Application\Router\RouteAction;
use App\EventSystem\EventMatcher;

final readonly class FindEvent implements RouteAction
{
    public function __construct(private EventMatcher $eventMatcher) {}

    public function handle(Request $request): Response
    {
        $params = $request->query['params'] ?? [];
        $event = $this->eventMatcher->findBestMatch($params);
        return Response::json($event ?? []);
    }
}
