<?php

declare(strict_types=1);

namespace App\Controller\Http;

use App\Core\Http\Message\Response;
use App\Core\Http\View\View;

abstract class AbstractController
{
    public function render(View $view, string $template, array $data = []): Response
    {
        return $view->render($template, $data);
    }

    public function json(array $data, int $httpCode = 200): Response
    {
        return new Response(
            json_encode($data),
            $httpCode,
            ['Content-Type: application/json; charset=utf-8'],
        );
    }
}
