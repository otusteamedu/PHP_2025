<?php

declare(strict_types=1);

namespace App\Core\Http\Controller\Base;

use App\Core\Http\ErrorHandler\ErrorHandlerInterface;
use App\Core\Http\Message\Response;

class ErrorController extends AbstractController
{
    public function __construct(
        private readonly ErrorHandlerInterface $errorHandler,
    ) {
    }

    public function get404Response(): Response
    {
        return $this->errorHandler->handle404();
    }
}
