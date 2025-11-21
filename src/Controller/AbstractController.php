<?php
declare(strict_types=1);

namespace Dinargab\Homework15\Controller;

abstract class AbstractController
{
    protected string $viewsPath = __DIR__ . '/../../views/';

    protected function render(string $viewName, array $data = []): void
    {
        $filePath = $this->viewsPath . $viewName . '.php';

        if (!file_exists($filePath)) {
            die("Error: View file '{$viewName}' not found at: " . $filePath);
        }

        extract($data);

        ob_start();

        require $filePath;

        $content = ob_get_clean();

        echo $content;
    }
}
