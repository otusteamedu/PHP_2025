<?php

declare(strict_types=1);

namespace Otus\Queue\Infrastructure\Template;

final readonly class Native implements TemplateInterface
{
    /**
     * @param string $path
     */
    public function __construct(
        private string $path,
    ) {
    }

    /**
     * @param string $template
     * @param array $data
     *
     * @return string
     *
     * @throws TemplateException
     */
    public function render(string $template, array $data = []): string
    {
        $file = $this->path . '/' . $template . '.php';

        if (!file_exists($file)) {
            throw new TemplateException(sprintf('Template not found: %s', $file));
        }

        extract($data, EXTR_SKIP);

        ob_start();

        require $file;

        return ob_get_clean() ?: '';
    }
}
