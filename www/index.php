<?php

require_once __DIR__ . '/vendor/autoload.php';

use Larkinov\Myapp\Class\App;

$app = new App();

echo $app->run();
