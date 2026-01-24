<?php
declare(strict_types=1);

namespace App\Presentation\Controllers;

use App\Domain\Interfaces\ActionInterface;
use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;

class EmailVerificationController
{
    /**
     * @var ActionInterface[]
     */
    private array $actions;

    /**
     * @param ActionInterface[] $actions
     */
    public function __construct(array $actions)
    {
        $this->actions = $actions;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function handleRequest(Request $request): Response
    {
        foreach ($this->actions as $action) {
            if ($action->supports($request)) {
                return $action->handle($request);
            }
        }

        return Response::html('Page not found. Path: ' . $request->getPath() . ', Method: ' . $request->getMethod(), 404);
    }
}
