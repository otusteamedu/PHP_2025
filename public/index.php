<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Application;

$response = Application::create()->run();
echo $response->send();