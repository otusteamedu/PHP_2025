<?php

declare(strict_types=1);

namespace App\View;

class View
{
    protected string $viewsDirectory;
    public function __construct($viewsDirectory = '')
    {
        $this->viewsDirectory = $viewsDirectory ?: __DIR__ . '/../views/';
    }

    public function render(string $templateName, array $data): string
    {
        $pathFile = $this->viewsDirectory . $templateName . '.php';

        if (!\is_file($pathFile)) {
            return '';
        }

        \ob_start();

        \extract($data);

        include $pathFile;

        $output = \ob_get_clean();

        if ($output === false) {
            return '';
        }

        return $output;
    }
}
