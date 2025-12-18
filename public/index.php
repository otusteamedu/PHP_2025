<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Dotenv\Exception\PathException;
use Symfony\Component\HttpFoundation\Request;

try {
    (new Dotenv())->load(\dirname(__DIR__) . '/.env.docker');
} catch (PathException $e) {
    echo $_SERVER['APP_ENV'] === 'dev' ? $e->getMessage() : '';
}

$kernel = new Kernel($_SERVER['APP_ENV'], (bool)$_SERVER['APP_DEBUG']);
$request = Request::createFromGlobals();
try {
    $response = $kernel->handle($request);
    $response->send();
    $kernel->terminate($request, $response);
} catch (Exception $e) {
    echo $_SERVER['APP_ENV'] === 'dev' ? $e->getMessage() : '';
}
