<?php declare(strict_types=1);

use App\App;

require __DIR__ . '/../vendor/autoload.php';

$app = new App();
try {
    $app->run()->send();
} catch (\Throwable $e) {
    http_response_code(500);
}
