<?php
declare(strict_types=1);

require __DIR__ . '/../composer/vendor/autoload.php';

use Zibrov\OtusPhp2025\App;

$app = new App();
try {
    $app->run();
} catch (\Exception $e) {
    throw new \RuntimeException($e->getMessage());
}
