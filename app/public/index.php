<?php declare(strict_types=1);

require(__DIR__ . '/../src/init.php');

use App\Application;

$application = new Application();
$application->run();
