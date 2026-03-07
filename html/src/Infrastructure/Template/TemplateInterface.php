<?php

declare(strict_types=1);

namespace Otus\Queue\Infrastructure\Template;

interface TemplateInterface
{
    /**
     * @param string $template
     * @param array $data
     *
     * @return string
     */
    public function render(string $template, array $data = []): string;
}
