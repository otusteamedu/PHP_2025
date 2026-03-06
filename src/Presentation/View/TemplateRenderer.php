<?php
declare(strict_types=1);

namespace App\Presentation\View;

class TemplateRenderer
{
    public function __construct(
        private readonly string $templatePath
    ) {}

    /**
     * @param string $template
     * @param array $data
     * @return string
     */
    public function render(string $template, array $data = []): string
    {
        $templateFile = rtrim($this->templatePath, '/') . '/' . $template;

        if (!file_exists($templateFile)) {
            throw new \RuntimeException("Template file not found: {$templateFile}");
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require $templateFile;
        return (string) ob_get_clean();
    }
}
