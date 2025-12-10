<?php
require_once __DIR__ . '/../vendor/autoload.php';

use src\App;

$config = require __DIR__ . '/../config/settings.php';

$app = new App($config);

$response = $app->run();

$response->send();
