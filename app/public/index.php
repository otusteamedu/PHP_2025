<?php
declare(strict_types=1);

use App\Application\App;

require __DIR__ . '/../vendor/autoload.php';

$app = new App();

$response = $app->run();
echo $response->body;
