<?php

declare(strict_types=1);

namespace App\Core\Http\ErrorHandler;

use App\Core\Http\Message\Response;
use App\Core\Http\View\View;

class WebErrorHandler implements ErrorHandlerInterface
{
    public function __construct(
        private readonly View $view,
    ) {
    }

    public function handle404(): Response
    {
        $response = $this->view->render('404.php');
        $response->setHttpCode(404);

        return $response;
    }
}
