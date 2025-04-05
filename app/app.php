<?php

declare(strict_types=1);

use App\App;

require_once dirname(__DIR__) . '/app/vendor/autoload.php';

$config = require(dirname(__DIR__) . '/app/config/config.php');
$app = new App($config);
echo $app->run();
