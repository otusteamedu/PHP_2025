<?php

declare(strict_types=1);

namespace App;

use Symfony\Component\HttpFoundation\Response;

class Application
{
    public function run(): void
    {
        $content = '';
        $response = new Response($content);
        $response->send();
    }
}
