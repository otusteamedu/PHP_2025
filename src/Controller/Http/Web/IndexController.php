<?php

declare(strict_types=1);

namespace App\Controller\Http\Web;

use App\Core\Http\Controller\Base\AbstractController;
use App\Core\Http\Message\Response;
use App\Core\Http\View\View;

class IndexController extends AbstractController
{
    public function __construct(
        private readonly View $view,
    ) {
    }

    public function displayMainPage(): Response
    {
        $response = $this->render($this->view, 'index.php');
        $response->setHeaders(['Cache-Control: no-cache']);

        return $response;
    }
}
