<?php

require_once __DIR__ . '/../app/vendor/autoload.php';

use Larkinov\Myapp\Class\App;

$app = new App();

try {
    $app->run();
    header('HTTP/1.1 200');
    return 'success';
} catch (\Throwable $th) {
    header('HTTP/1.1 400 Bad Request');
    return $th->getMessage();
}
