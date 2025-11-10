<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$app = new App\App();
$response = $app->run();

http_response_code($response->getStatusCode());
foreach ($response->getHeaders() as $name => $values) {
    $normalized = implode('-', array_map(static function ($part) { return ucfirst($part); }, explode('-', (string)$name)));
    foreach ($values as $value) {
        header($normalized . ': ' . $value, false);
    }
}

echo (string)$response->getBody();