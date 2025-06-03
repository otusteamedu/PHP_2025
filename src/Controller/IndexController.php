<?php

declare(strict_types=1);

namespace App\Controller;

class IndexController
{
    public function displayMainPage(): void
    {
        include __DIR__ . '/../../templates/index.php';
    }
}
