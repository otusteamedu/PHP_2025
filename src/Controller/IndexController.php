<?php

declare(strict_types=1);

namespace App\Controller;

use App\Application\Response;

class IndexController extends AbstractController
{
    public function displayMainPage(): Response
    {
        return $this->view->render('index.php')->setHeaders(['Cache-Control: no-cache']);
    }
}
