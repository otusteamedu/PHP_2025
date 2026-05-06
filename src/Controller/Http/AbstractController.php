<?php

declare(strict_types=1);

namespace App\Controller\Http;

use App\Core\Http\Request;
use App\Core\Http\View;

abstract class AbstractController
{
    protected readonly Request $request;
    protected readonly View $view;

    public function __construct()
    {
        $this->request = new Request();
        $this->view = new View();
    }
}
