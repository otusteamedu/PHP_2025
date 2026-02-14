<?php

declare(strict_types=1);

namespace App\Presentation\Controllers\Actions;

use App\Domain\Interfaces\GetMenuUseCaseInterface;
use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;
use App\Domain\Interfaces\ActionInterface;

class GetMenuAction implements ActionInterface
{
    public function __construct(
        private GetMenuUseCaseInterface $getMenuUseCase
    ) {}

    public function supports(Request $request): bool
    {
        return $request->isGet() && $request->getPath() === '/menu';
    }

    public function handle(Request $request): Response
    {
        $menu = $this->getMenuUseCase->execute();
        return Response::success($menu);
    }
}
