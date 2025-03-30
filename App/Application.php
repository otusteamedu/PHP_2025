<?php

declare(strict_types=1);

namespace App;

use Symfony\Component\HttpFoundation\Response;

class Application
{
    public function run(): void
    {
        $content = 'Здесь не чего нету. Это консольное приложение, смотри App/bin/console';
        $response = new Response($content);
        $response->send();
    }
}
