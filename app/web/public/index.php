<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../vendor/autoload.php';

use SergeyGolovanov\HW4\Application\App;

$app = new App();
$app->run($_REQUEST);