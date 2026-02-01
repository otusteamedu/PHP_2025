<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\View;

use App\Domain\Service\View\ViewInterface;

class PhpTemplateView implements ViewInterface
{
    private string $templatePath;
    private string $headerPath;
    private string $footerPath;

    public function __construct(string $templatePath, string $headerPath, string $footerPath)
    {
        // Проверяем, что пути существуют, чтобы избежать ошибок в будущем
        if (!is_dir($templatePath)) {
            throw new \InvalidArgumentException("Template directory not found: {$templatePath}");
        }
        if (!file_exists($headerPath)) {
            throw new \InvalidArgumentException("Header file not found: {$headerPath}");
        }
        if (!file_exists($footerPath)) {
            throw new \InvalidArgumentException("Footer file not found: {$footerPath}");
        }

        $this->templatePath = rtrim($templatePath, '/');
        $this->headerPath = $headerPath;
        $this->footerPath = $footerPath;
    }

    public function render(string $template, array $data = []): string
    {
        $content = $this->renderPartial($template, $data);

        ob_start();
        require $this->headerPath;
        echo $content;
        require $this->footerPath;
        return ob_get_clean();
    }

    public function renderPartial(string $template, array $data = []): string
    {
        $templateFile = $this->templatePath . '/' . $template . '.php';

        if (!file_exists($templateFile)) {
            throw new \RuntimeException("View template not found: {$templateFile}");
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require $templateFile;
        return ob_get_clean();
    }
}
