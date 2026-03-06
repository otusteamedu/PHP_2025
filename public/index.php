<?php
declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

use App\Config\AppConfig;
use App\Config\AppFactory;

$app = (new AppFactory(AppConfig::fromEnvironment()))->createHttpApplication();
echo $app->run()->send();
