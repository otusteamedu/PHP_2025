<?php

namespace Crowley\App\Kernel;

class App
{
    private string $uri;
    private Router $router;

    public function __construct() {
        $this->uri = $_SERVER['REQUEST_URI'];


        try {
            $this->router = new Router($this->uri);
        } catch (\Throwable $e) {
            echo "<pre>". $e->getMessage() ."</pre>";
        }

    }

}
