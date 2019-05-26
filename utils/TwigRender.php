<?php

namespace app\utils;


use app\interfaces\IRender;

class TwigRender implements IRender
{

    private $loader;
    private $twig;

    public function __construct()
    {
        $this->loader = new \Twig\Loader\FilesystemLoader('../templates');
        $this->twig = new \Twig\Environment($this->loader, [
//    'cache' => '/path/to/compilation_cache',
        ]);
    }

    public function renderTemplate($template, $params = [])
    {
        return $this->twig->render($template, $params);
    }
}