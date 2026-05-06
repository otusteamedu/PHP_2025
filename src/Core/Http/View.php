<?php

declare(strict_types=1);

namespace App\Core\Http;

use App\Core\Utils\PathResolver;

class View
{
    private array $data = [];
    private readonly string $templatesPath;

    public function __construct()
    {
        $this->templatesPath = PathResolver::build('/templates');
    }

    public function __get(string $name): mixed
    {
        return $this->data[$name];
    }

    public function __set(string $name, mixed $value): void
    {
        $this->data[$name] = $value;
    }

    public function render(string $template): Response
    {
        extract($this->data);

        ob_start();
        include "$this->templatesPath/$template";
        $content = ob_get_contents();
        ob_end_clean();

        return new Response($content);
    }
}
