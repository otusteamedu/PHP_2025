<?php

declare(strict_types=1);

namespace App\Core\Http\View;

use App\Core\Http\Message\Response;
use App\Core\Utils\PathResolverInterface;

class View
{
    private readonly string $templatesPath;

    public function __construct(PathResolverInterface $pathResolver)
    {
        $this->templatesPath = $pathResolver->getTemplatesPath();
    }

    public function render(string $template, array $data = []): Response
    {
        $templatePath = "$this->templatesPath/$template";

        if (!file_exists($templatePath)) {
            throw new \RuntimeException("Template '$template' not found at '$templatePath'.");
        }

        extract($data);

        ob_start();
        include $templatePath;
        $content = ob_get_contents();
        ob_end_clean();

        return new Response($content);
    }
}
