<?php

declare(strict_types=1);

namespace App\Core\Http\ErrorHandler;

use App\Core\Container\Providers\UiServiceProvider;
use App\Core\Http\Message\Request;
use App\Core\Http\View\View;

class ErrorHandlerFactory
{
    public function __construct(
        private readonly ?View $view,
    ) {
    }

    public function create(Request $request): ErrorHandlerInterface
    {
        if (str_starts_with($request->getRequestPath(), '/api/')) {
            return new ApiErrorHandler();
        }

        if ($this->view === null) {
            throw new \RuntimeException(
                'View component not registered in container. Use ' . UiServiceProvider::class . ' for register.'
            );
        }

        return new WebErrorHandler($this->view);
    }
}
