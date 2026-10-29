<?php

declare(strict_types=1);

namespace App\Core\Http\ErrorHandler;

use App\Core\Container\Context\ContextDetector;
use App\Core\Container\Context\ContextType;
use App\Core\Container\Providers\UiServiceProvider;
use App\Core\Http\View\View;

class ErrorHandlerFactory
{
    public function __construct(
        private readonly ContextDetector $contextDetector,
        private readonly ?View $view,
        private readonly ?ExceptionResponseResolverInterface $responseResolver = null,
    ) {
    }

    public function create(): ErrorHandlerInterface
    {
        if ($this->contextDetector->detectContext() === ContextType::HTTP_API) {
            return new ApiErrorHandler($this->responseResolver);
        }

        if ($this->view === null) {
            throw new \LogicException(
                'View component not registered in container. Use ' . UiServiceProvider::class . ' for register.'
            );
        }

        return new WebErrorHandler($this->view);
    }
}
