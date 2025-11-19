<?php

namespace App\Base\Views;

use InvalidArgumentException;

class ViewManager
{
    public function __construct(protected string $viewsPath = __DIR__ . '/../../views')
    {
    }

    public function renderTemplate(string $view, array $arguments = []): void
    {
        extract($arguments);
        $viewPath = join('/', [$this->viewsPath, "$view.php"]);
        if (!file_exists($viewPath)) {
            throw new InvalidArgumentException("View $view does not exist");
        }
        include $viewPath;
    }
}