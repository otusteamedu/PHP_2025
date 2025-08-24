<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Larkinov\Myapp\Classes\App;

$app = new App();

try {
    $app->run();
    header('HTTP/1.1 200');
    return 'success';
} catch (\Throwable $th) {
    header('HTTP/1.1 400 Bad Request');
    return $th->getMessage();
}
