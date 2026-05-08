<?php

declare(strict_types=1);

use App\Application\Console\App;

require __DIR__ . '/vendor/autoload.php';

$app = new App();
$app->run($argv);
