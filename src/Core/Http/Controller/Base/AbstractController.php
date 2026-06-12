<?php

declare(strict_types=1);

namespace App\Core\Http\Controller\Base;

use App\Core\Http\Message\Response;
use App\Core\Http\View\View;

abstract class AbstractController
{
    use ApiResponseTrait;

    protected function render(View $view, string $template, array $data = []): Response
    {
        return $view->render($template, $data);
    }

    protected function json(array $data, int $httpCode = 200): Response
    {
        return new Response(json_encode($data), $httpCode, ['Content-Type: application/json; charset=utf-8']);
    }

    protected function handleOperation(callable $operation): Response
    {
        return $this->apiResponse($operation);
    }
}
