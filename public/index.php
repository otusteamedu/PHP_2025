<?php declare(strict_types=1);

use App\App;
use App\Http\Response;

require(__DIR__ . '/../vendor/autoload.php');

try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();

    $app = new App();
    $response = $app->run();

    echo $response;
} catch (\Throwable $e) {
    echo (new Response())->send(500, ['error' => 'Internal server error']);
}
