<?php

declare(strict_types=1);

use \Pryaniki\App\App;

require '../app/vendor/autoload.php';

$app = new App();

$result = $app->run();
echo $result;

