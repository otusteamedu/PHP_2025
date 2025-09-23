<?php
declare(strict_types = 1);

namespace App\EventSystem\ApiActions;

use App\Application\Http\Request;
use App\Application\Http\Response;
use App\Application\Router\RouteAction;
use App\EventSystem\EventSystemRepository;

final readonly class AddEvent implements RouteAction
{
    public function __construct(private EventSystemRepository $repository) {}

    public function handle(Request $request): Response
    {
        $data = $request->json;
        $priority = (int) $data[ 'priority' ];
        $conditions = $this->normalizeConditions((array) $data[ 'conditions' ]);
        $event = (array) $data[ 'event' ];
        $id = $this->repository->add($priority, $conditions, $event);

        return Response::json(['id' => $id]);
    }

    private function normalizeConditions(array $conditions): array
    {
        $normalized = [];
        foreach ($conditions as $key => $value) {
            $normalized[ $key ] = (string) $value;
        }
        return $normalized;
    }
}
