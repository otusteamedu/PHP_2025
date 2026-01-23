<?php
declare(strict_types=1);

namespace App\Presentation\Actions;

use App\Domain\Interfaces\ActionInterface;
use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;
use App\Presentation\Views\ViewRenderer;

class TemplateRenderAction implements ActionInterface
{
    private ViewRenderer $viewRenderer;

    public function __construct(ViewRenderer $viewRenderer)
    {
        $this->viewRenderer = $viewRenderer;
    }

    public function supports(Request $request): bool
    {
        return $request->isGet();
    }

    /**
     * @throws \Exception
     */
    public function handle(Request $request): Response
    {
        $content = $this->viewRenderer->render('verify.html');

        return Response::html($content);
    }
}
