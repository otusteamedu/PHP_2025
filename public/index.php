<?php
declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

use App\Config\Application;

$app = Application::build();
$app->run();
