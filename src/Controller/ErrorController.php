<?php

declare(strict_types=1);

namespace App\Controller;

use App\Application\Response;

class ErrorController extends AbstractController
{
    public function render404Page(): Response
    {
        return $this->view->render('404.php')->setHttpCode(404);
    }
}
