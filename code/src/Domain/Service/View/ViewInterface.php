<?php

declare(strict_types=1);

namespace App\Domain\Service\View;

interface ViewInterface
{
    /**
     * @param string $template
     * @param array $data
     * @return string
     */
    public function renderPartial(string $template, array $data = []): string;

    /**
     * @param string $template
     * @param array $data
     * @return string
     */
    public function render(string $template, array $data = []): string;
}
