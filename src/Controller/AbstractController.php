<?php

declare(strict_types=1);

namespace App\Controller;

use App\Application\Request;
use App\Application\View;

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
