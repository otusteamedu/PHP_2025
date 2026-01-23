<?php
declare(strict_types=1);

namespace App\Presentation\Views;

class ViewRenderer
{
    private string $templatePath;

    public function __construct(string $templatePath)
    {
        $this->templatePath = rtrim($templatePath, '/');
    }

    /**
     * @param string $template
     * @param array $data
     * @return string
     * @throws \Exception
     */
    public function render(string $template, array $data = []): string
    {
        $templateFile = $this->templatePath . '/' . $template;

        if (!file_exists($templateFile)) {
            throw new \Exception("Template file not found: {$templateFile}. Path: {$this->templatePath}");
        }

        extract($data, EXTR_SKIP);

        ob_start();
        include $templateFile;
        return ob_get_clean();
    }
}
