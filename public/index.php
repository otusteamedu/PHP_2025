<?php

declare(strict_types=1);

use App\Application\App;

require_once __DIR__ . '/../vendor/autoload.php';

$app = new App();
echo $app->run();
