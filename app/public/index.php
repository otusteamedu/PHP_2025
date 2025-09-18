<?php
declare(strict_types=1);

use App\Application\App;
use App\Service\LastCheckService;
use App\Service\ParenthesesValidator;
use App\View\Renderer;

require __DIR__ . '/../vendor/autoload.php';

session_start();

$app = new App(
    new ParenthesesValidator(),
    new Renderer(),
    new LastCheckService()
);

$response = $app->run();
http_response_code($response->statusCode);
echo $response->body;
