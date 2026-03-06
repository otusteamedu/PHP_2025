<?php
declare(strict_types=1);

use Pryaniki\App\Presentation\Console\ConsoleSearchRunner;

require __DIR__ . '/../vendor/autoload.php';

$app = new ConsoleSearchRunner();
$app->run();
