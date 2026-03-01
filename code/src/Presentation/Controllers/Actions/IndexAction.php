<?php

declare(strict_types=1);

namespace App\Presentation\Controllers\Actions;

use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;
use App\Domain\Interfaces\ActionInterface;

class IndexAction implements ActionInterface
{
    private const FORM_PATH = __DIR__ . '/../../../../public/form.html';

    public function supports(Request $request): bool
    {
        return $request->isGet() && $request->getPath() === '/';
    }

    public function handle(Request $request): Response
    {
        $html = file_get_contents(self::FORM_PATH);

        return Response::html($html);
    }
}
