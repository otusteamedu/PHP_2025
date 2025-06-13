<?php

require __DIR__ . './../vendor/autoload.php';

use Tenshuu\OtusPackageTest\App\Service\TextService;

try {
    $service = new TextService();

    echo $service->getText();
} catch (Throwable $e) {
    echo $e->getMessage();
}
