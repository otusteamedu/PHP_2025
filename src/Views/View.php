<?php
namespace App\Views;

class View
{
    private string $basePath;

    public function __construct(?string $basePath = null)
    {
        $this->basePath = $basePath ?? dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'views';
    }

    public function render(string $template, array $params = []): string
    {
        $path = rtrim($this->basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $template . '.php';
        if (!is_file($path)) {
            throw new \RuntimeException("View template not found: {$path}");
        }
        extract($params, EXTR_SKIP);
        ob_start();
        try {
            include $path;
        } finally {
            return ob_get_clean();
        }
    }
}
